<?php

namespace App\Http\Controllers;

use App\Models\DocumentLog;
use App\Models\OfficialData;
use App\Models\SignatureRequest;
use App\Notifications\SignatureApprovedNotification;
use App\Notifications\SignatureRejectedNotification;
use App\Notifications\SignatureRequestedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SignatureRequestController extends Controller
{
    public function create(DocumentLog $documentLog) {
        $this->authorizeDocumentLog($documentLog);

        if (!file_exists(public_path('cached_result/' . $documentLog->output_filename))) {
            return back()->with('error', 'File dokumen sudah dihapus dari server. Silakan buat ulang dokumen terlebih dahulu.');
        }

        $existing = SignatureRequest::where('document_log_id', $documentLog->id)
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existing) {
            return back()->with('error', 'Dokumen ini sudah memiliki permintaan tanda tangan yang aktif.');
        }

        $officials = OfficialData::orderBy('staff_name')->get();

        return view('signature.create', compact('documentLog', 'officials'));
    }

    public function store(Request $request, DocumentLog $documentLog)
    {
        $this->authorizeDocumentLog($documentLog);

        $request->validate([
            'official_id' => 'required|exists:official_data,id',
        ]);

        if (!file_exists(public_path('cached_result/' . $documentLog->output_filename))) {
            return back()->with('error', 'File dokumen sudah dihapus. Buat ulang dokumen terlebih dahulu.');
        }

        $existing = SignatureRequest::where('document_log_id', $documentLog->id)
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existing) {
            return back()->with('error', 'Permintaan tanda tangan sudah ada untuk dokumen ini.');
        }

        $official = OfficialData::findOrFail($request->official_id);

        $signatureRequest = SignatureRequest::create([
            'user_id' => auth()->id(),
            'document_log_id' => $documentLog->id,
            'official_id' => $official->id,
            'status' => 'pending',
            'token' => SignatureRequest::generateToken(),
            'requested_at' => now(),
        ]);

        // Send email to the official
        try {
            $official->notify(new SignatureRequestedNotification($signatureRequest));
        } catch (\Exception $e) {
            Log::error('Failed to send signature request email: ' . $e->getMessage());
            // Don't fail the request — just log it
        }

        return redirect()->route('home')
            ->with('success', "Permintaan tanda tangan berhasil dikirim ke {$official->staff_name}. Anda akan menerima notifikasi via email setelah ditinjau.");
    }

    public function review(string $token)
    {
        $signatureRequest = SignatureRequest::where('token', $token)
            ->with(['documentLog', 'official', 'user'])
            ->firstOrFail();

        if (!$signatureRequest->isPending()) {
            return view('signature.already-reviewed', compact('signatureRequest'));
        }

        return view('signature.review', compact('signatureRequest'));
    }

    public function processReview(Request $request, string $token)
    {
        $signatureRequest = SignatureRequest::where('token', $token)
            ->with(['documentLog', 'official', 'user'])
            ->firstOrFail();

        if (!$signatureRequest->isPending()) {
            return redirect()->route('signature.review', $token)
                ->with('error', 'Permintaan ini sudah ditinjau sebelumnya.');
        }

        $request->validate([
            'decision' => 'required|in:approved,rejected',
            'notes' => 'nullable|string|max:1000',
        ]);

        $decision = $request->decision;

        $signatureRequest->update([
            'status' => $decision,
            'notes' => $request->notes,
            'reviewed_at' => now(),
        ]);

        // Notify the requesting user
        try {
            $user = $signatureRequest->user;
            if ($user && $user->email) {
                if ($decision === 'approved') {
                    $user->notify(new SignatureApprovedNotification($signatureRequest));
                } else {
                    $user->notify(new SignatureRejectedNotification($signatureRequest));
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to send signature result email: ' . $e->getMessage());
        }

        $label = $decision === 'approved' ? 'disetujui' : 'ditolak';
        return view('signature.review-done', compact('signatureRequest', 'label'));
    }

    public function adminIndex(Request $request)
    {
        $query = SignatureRequest::with(['user', 'documentLog.documentType', 'official'])
            ->latest('requested_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->paginate(20)->withQueryString();

        $pendingCount = SignatureRequest::where('status', 'pending')->count();

        return view('admin.admin-signatures', compact('requests', 'pendingCount'));
    }

    public function adminApprove(Request $request, SignatureRequest $signatureRequest)
    {
        if (!$signatureRequest->isPending()) {
            return back()->with('error', 'Permintaan ini sudah ditinjau.');
        }

        $request->validate(['notes' => 'nullable|string|max:1000']);

        $signatureRequest->update([
            'status' => 'approved',
            'notes' => $request->notes,
            'reviewed_at' => now(),
        ]);

        try {
            $user = $signatureRequest->user;
            if ($user) {
                $user->notify(new SignatureApprovedNotification($signatureRequest));
            }
        } catch (\Exception $e) {
            Log::error('Admin approve email failed: ' . $e->getMessage());
        }

        return back()->with('success', 'Permintaan berhasil disetujui dan notifikasi dikirim ke pemohon.');
    }

    public function adminReject(Request $request, SignatureRequest $signatureRequest)
    {
        if (!$signatureRequest->isPending()) {
            return back()->with('error', 'Permintaan ini sudah ditinjau.');
        }

        $request->validate(['notes' => 'nullable|string|max:1000']);

        $signatureRequest->update([
            'status' => 'rejected',
            'notes' => $request->notes ?? 'Ditolak oleh Administrator.',
            'reviewed_at' => now(),
        ]);

        try {
            $user = $signatureRequest->user;
            if ($user) {
                $user->notify(new SignatureRejectedNotification($signatureRequest));
            }
        } catch (\Exception $e) {
            Log::error('Admin reject email failed: ' . $e->getMessage());
        }

        return back()->with('success', 'Permintaan berhasil ditolak dan notifikasi dikirim ke pemohon.');
    }

    private function authorizeDocumentLog(DocumentLog $documentLog): void
    {
        $user = auth()->user();
        if (!$user) {
            abort(403);
        }
        // Admin can do anything; regular users only their own logs
        if (!$user->isAdmin() && $documentLog->user_id !== $user->id) {
            abort(403);
        }
    }
}
