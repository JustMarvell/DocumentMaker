<?php

namespace App\Http\Controllers;

use App\Models\DocumentField;
use App\Models\DocumentLog;
use App\Models\DocumentType;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;

class DocumentController extends Controller
{
    public function index() {

    $role = auth()->check() ? auth()->user()->role : 'guest';

        $documentTypes = DocumentType::active()
            ->accessibleBy($role)
            ->orderBy('name')
            ->get();

        $allFields = DocumentField::whereIn('document_type_id', $documentTypes->pluck('id'))
            ->orderBy('sort_order')
            ->get()
            ->groupBy('document_type_id');

        return view('home', compact('documentTypes', 'allFields'));
    }

    public function generate(Request $request) {
        $request->validate([
            'letter-type' => 'required|string|exists:document_types,key',
        ]);

        $role = auth()->check() ? auth()->user()->role : 'guest';

        $documentType = DocumentType::where('key', $request->input('letter-type'))
            ->active()
            ->accessibleBy($role)
            ->with('fields')
            ->firstOrFail();

        $rules = [];

        foreach ($documentType->fields as $field) {
            if ($field->is_group_child)
                continue;

            if ($field->field_type === 'repeating_group') {
                $rules["field_{$field->field_key}"] = 'nullable|array';
                // Validate each child field inside every row
                $children = $documentType->fields
                    ->where('is_group_child', true)
                    ->where('group_key', $field->field_key);
                foreach ($children as $child) {
                    $rules["field_{$field->field_key}.*.{$child->field_key}"] =
                        $child->is_required ? 'required|string' : 'nullable|string';
                }
                continue;
            }

            $rule = $field->is_required ? 'required' : 'nullable';
            $rule .= '|' . match ($field->field_type) {
                'date' => 'date',
                'number' => 'numeric',
                'checkbox' => 'boolean',
                default => 'string',
            };

            $rules["field_{$field->field_key}"] = $rule;
        }

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // $validator = Validator::make($request->all(), $rules);
        // if ($validator->fails()) {
        //     dd([
        //         'errors' => $validator->errors()->toArray(),
        //         'rules' => $rules,
        //         'request_data' => $request->all(),
        //         'role' => $role,
        //         'doctype' => $documentType->key,
        //     ]);
        // }

        $context = [];

        foreach ($documentType->fields as $field) {
            if ($field->is_group_child)
                continue;

            if ($field->field_type === 'repeating_group') {
                $context[$field->field_key] = $request->input("field_{$field->field_key}", []);
            } elseif ($field->field_type === 'checkbox') {
                $context[$field->field_key] = (bool) $request->input("field_{$field->field_key}", false);
            } else {
                $context[$field->field_key] = $request->input("field_{$field->field_key}", '');
            }
        }

        return $this->runScript($documentType, $context);
    }

    public function download(string $filename) {
        $filename = basename($filename);
        $path = public_path("cached_result/{$filename}");

        abort_unless(file_exists($path), 404);

        DocumentLog::where('output_filename', $filename)
            ->whereNull('downloaded_at')
            ->update(['downloaded_at' => now()]);

        return response()->download($path)->deleteFileAfterSend(false);
    }

    private function runScript(DocumentType $documentType, array $context)
    {
        $pythonBin  = base_path('venv/bin/python');
        $scriptPath = base_path("scripts/{$documentType->script_name}");
 
        $extension      = pathinfo($documentType->template_filename, PATHINFO_EXTENSION);
        $uniqueFilename = Str::slug($documentType->output_filename) . '_' . Str::uuid() . '.' . $extension;
 
        $cmd = [
            $pythonBin,
            $scriptPath,
            '--template',        $documentType->template_filename,
            '--output-filename', $uniqueFilename,
            '--context',         json_encode($context, JSON_UNESCAPED_UNICODE),
        ];
 
        $process = new Process($cmd);
        $process->setTimeout(60);
        $process->run();
 
        $status = $process->isSuccessful() ? 'success' : 'failed';
 
        DocumentLog::create([
            'user_id'          => auth()->id(),
            'document_type_id' => $documentType->id,
            'output_filename'  => $uniqueFilename,
            'status'           => $status,
            'generated_at'     => now(),
        ]);
 
        if (!$process->isSuccessful()) {
            Log::error("Document generation failed [{$documentType->script_name}]: " . $process->getErrorOutput());
            return back()->with('error', 'Gagal membuat dokumen. Silakan coba lagi atau hubungi administrator.');
        }
 
        return back()
            ->with('success', 'Dokumen berhasil dibuat!')
            ->with('download_url', route('document.download', ['filename' => $uniqueFilename]));
    }

    // // letter of assignment
    // private function generateLetterOfAssignment(Request $request, DocumentType $documentType) {
    //     $request->validate([
    //         'la_employee_name'      => 'required|string',
    //         'la_employee_position'  => 'required|string',
    //         'la_employee_address'   => 'required|string',
    //         'la_letter_number'      => 'required|string',
    //         'la_letter_date'        => 'required|date',
    //         'la_assignment_objective' => 'required|string',
    //         'la_destination_agency' => 'required|string',
    //         'la_departure_date'     => 'required|date',
    //         'la_return_date'        => 'required|date',
    //     ]);

    //     $args = [
    //         '--letter-number'              => $request->la_letter_number,
    //         '--assignment-objective'       => $request->la_assignment_objective,
    //         '--destination-agency-location'=> $request->la_destination_agency,
    //         '--employee-name'              => $request->la_employee_name,
    //         '--employee-position'          => $request->la_employee_position,
    //         '--employee-address'           => $request->la_employee_address,
    //         '--departure-date'             => $request->la_departure_date,
    //         '--return-date'                => $request->la_return_date,
    //         '--letter-date'                => $request->la_letter_date,
    //     ];

    //     return $this->runPythonScript($documentType, $args);
    // }

    // private function generatePermissionLetter(Request $request, DocumentType $documentType) {
    //     $request->validate([
    //         'pl_employee_name'     => 'required|string',
    //         'pl_employee_position' => 'required|string',
    //         'pl_employee_address'  => 'required|string',
    //         'pl_letter_date'       => 'required|date',
    //         'pl_target_name'       => 'required|string',
    //         'pl_target_address'    => 'required|string',
    //         'pl_total_sick_day'    => 'required|integer|min:1',
    //         'pl_start_date'        => 'required|date',
    //         'pl_end_date'          => 'required|date|after_or_equal:pl_start_date',
    //     ]);

    //     $args = [
    //         '--employee-name'      => $request->pl_employee_name,
    //         '--employee-position'  => $request->pl_employee_position,
    //         '--employee-address'   => $request->pl_employee_address,
    //         '--employee-id-number' => $request->pl_employee_id_number ?? '',
    //         '--letter-address'     => $request->pl_letter_address ?? '',
    //         '--letter-date'        => $request->pl_letter_date,
    //         '--attachment-count'   => $request->pl_attachment_count ?? '0',
    //         '--target-name'        => $request->pl_target_name,
    //         '--target-address'     => $request->pl_target_address,
    //         '--total-sick-day'     => $request->pl_total_sick_day,
    //         '--start-date'         => $request->pl_start_date,
    //         '--end-date'           => $request->pl_end_date,
    //     ];

    //     return $this->runPythonScript($documentType, $args);
    // }

    // private function generateEmployeePerformanceTargets(Request $request, DocumentType $documentType) {
    //     $request->validate([
    //         'ept_appraisal_period_start' => 'required|date',
    //         'ept_appraisal_period_end' => 'required|date|after_or_equal:ept_appraisal_period_start',
    //         'ept_employee_name' => 'required|string',
    //         'ept_employee_nip' => 'required|string',
    //         'ept_employee_rank' => 'required|string',
    //         'ept_employee_position' => 'required|string',
    //         'ept_employee_work_unit' => 'required|string',
    //         'ept_appraisal_name' => 'required|string',
    //         'ept_appraisal_nip' => 'required|string',
    //         'ept_appraisal_rank' => 'required|string',
    //         'ept_appraisal_position' => 'required|string',
    //         'ept_appraisal_work_unit' => 'required|string',
    //         'ept_leadership_work_result_plan' => 'required|string',
    //         'ept_work_result_plan' => 'required|string',
    //         'ept_work_quantity_indicator' => 'required|string',
    //         'ept_work_quality_indicator' => 'required|string',
    //         'ept_work_time_indicator' => 'required|string',
    //         'ept_work_quantity_target' => 'required|string',
    //         'ept_work_quality_target' => 'required|string',
    //         'ept_work_time_target' => 'required|string',
    //         'ept_additional_work_behaviour_1' => 'required|string',
    //         'ept_additional_work_behaviour_1_description' => 'required|string',
    //         'ept_leadership_spesific_expectation' => 'required|string',
    //     ]);

    //     $args = [
    //         '--appraisal-period-start' => $request->ept_appraisal_period_start,
    //         '--appraisal-period-end' => $request->ept_appraisal_period_end,
    //         '--employee-name' => $request->ept_employee_name,
    //         '--employee-nip' => $request->ept_employee_nip,
    //         '--employee-rank' => $request->ept_employee_rank,
    //         '--employee-position' => $request->ept_employee_position,
    //         '--employee-work-unit' => $request->ept_employee_work_unit,
    //         '--appraisal-name' => $request->ept_appraisal_name,
    //         '--appraisal-nip' => $request->ept_appraisal_nip,
    //         '--appraisal-rank' => $request->ept_appraisal_rank,
    //         '--appraisal-position' => $request->ept_appraisal_position,
    //         '--appraisal-work-unit' => $request->ept_appraisal_work_unit,
    //         '--leadership-work-result-plan' => $request->ept_leadership_work_result_plan,
    //         '--work-result-plan' => $request->ept_work_result_plan,
    //         '--work-quantity-indicator' => $request->ept_work_quantity_indicator,
    //         '--work-quality-indicator' => $request->ept_work_quality_indicator,
    //         '--work-time-indicator' => $request->ept_work_time_indicator,
    //         '--work-quantity-target' => $request->ept_work_quantity_target,
    //         '--work-quality-target' => $request->ept_work_quality_target,
    //         '--work-time-target' => $request->ept_work_time_target,
    //         '--additional-work-behaviour-1' => $request->ept_additional_work_behaviour_1,
    //         '--additional-work-behaviour-1-description' => $request->ept_additional_work_behaviour_1_description,
    //         '--leadership-spesific-expectation' => $request->ept_leadership_spesific_expectation,
    //     ];

    //     return $this->runPythonScript($documentType, $args);
    // }

    // /**
    //  * shared function to run python script
    //  */
    // private function runPythonScript(DocumentType $documentType, array $args) {

    //     $pythonBin = base_path('venv/bin/python');
    //     $scriptPath = base_path("scripts/{$documentType->script_name}");

    //     $ext = pathinfo($documentType->output_filename, PATHINFO_EXTENSION);
    //     $basename = $ext ? pathinfo($documentType->output_filename, PATHINFO_FILENAME) : $documentType->output_filename;
    //     $extension = $ext ?: pathinfo($documentType->template_filename, PATHINFO_EXTENSION);
    //     $uniqueFilename = $basename . '_' . Str::uuid() . '.' . $extension;

    //     $args['--output-filename'] = $uniqueFilename;

    //     $cmd = [$pythonBin, $scriptPath];
    //     foreach ($args as $flag => $value) {
    //         $cmd[] = $flag;
    //         $cmd[] = (string) $value;
    //     }

    //     $process = new Process($cmd);
    //     $process->setTimeout(60);
    //     $process->run();

    //     $status = $process->isSuccessful() ? 'success' : 'failed';

    //     DocumentLog::create([
    //         'user_id' => auth()->id(),
    //         'document_type_id' => $documentType->id,
    //         'output_filename' => $uniqueFilename,
    //         'status' => $status,
    //         'generated_at' => now(),
    //     ]);

    //     if (!$process->isSuccessful()) {
    //         Log::error("Document Generation Failed [{$documentType->script_name}]: " . $process->getErrorOutput());
    //         return back()->with('error', 'Gagal Membuat surat. Silahkan Coba Lagi atau Hubungi Administrator.');
    //     }

    //     $downloadUrl = route('document.download', ['filename' => $uniqueFilename]);

    //     return back()
    //         ->with('success', 'Surat Berhasil di Buat!')
    //         ->with('download_url', $downloadUrl);
    // }
}
