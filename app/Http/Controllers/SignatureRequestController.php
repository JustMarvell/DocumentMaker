<?php

namespace App\Http\Controllers;

use App\Models\DocumentLog;
use App\Models\OfficialData;
use App\Models\SignatureRequest;
use App\Notifications\SignatureApprovedNotification;
use App\Notifications\SignatureRejectedNotification;
use App\Notifications\SignatureRequestedNotification;
use App\Services\SignatureService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SignatureRequestController extends Controller
{
    public function __construct(private SignatureService $signingService)
    {
    }

    // User: Request a signature 

    public function create(DocumentLog $documentLog)
    {
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

        try {
            $official->notify(new SignatureRequestedNotification($signatureRequest));
        } catch (\Exception $e) {
            Log::error('Failed to send signature request email: ' . $e->getMessage());

            return redirect()->route('home')
                ->with('email_warning', 'Notifikasi email gagal dikirim ke pejabat. Gunakan tombol kirim ulang jika diperlukan.');
        }

        return redirect()->route('home')
            ->with('success', "Permintaan tanda tangan berhasil dikirim ke {$official->staff_name}. Anda akan menerima notifikasi via email setelah ditinjau.");
    }

    // Official: Review via email token

    public function review(string $token)
    {
        $signatureRequest = SignatureRequest::where('token', $token)
            ->with(['documentLog.documentType', 'official', 'user'])
            ->firstOrFail();

        if (!$signatureRequest->isPending()) {
            return view('signature.already-reviewed', compact('signatureRequest'));
        }

        return view('signature.review', compact('signatureRequest'));
    }

    public function processReview(Request $request, string $token)
    {
        $signatureRequest = SignatureRequest::where('token', $token)
            ->with(['documentLog.documentType', 'official', 'user'])
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

        // Update status first so reviewed_at is set before signing
        $signatureRequest->update([
            'status' => $decision,
            'notes' => $request->notes,
            'reviewed_at' => now(),
        ]);

        // If approved, run the signing script
        if ($decision === 'approved') {
            $signedFilename = $this->signingService->sign($signatureRequest);
            if ($signedFilename) {
                $signatureRequest->update(['signed_filename' => $signedFilename]);
            }
            $signatureRequest = SignatureRequest::with(['documentLog.documentType', 'official', 'user'])
                ->find($signatureRequest->id);
        }

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

    // public : verify doc

    public function verify(string $token)
    {
        $signatureRequest = SignatureRequest::where('token', $token)
            ->with(['documentLog.documentType', 'official', 'user'])
            ->firstOrFail();

        return view('signature.verify', compact('signatureRequest'));
    }

    // admin queue management

    public function adminIndex(Request $request)
    {
        $query = SignatureRequest::with(['user', 'documentLog.documentType', 'official'])
            ->latest('requested_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->paginate(20)->withQueryString();
        $pendingCount = SignatureRequest::where('status', 'pending')->count();

        return view('admin.signatures', compact('requests', 'pendingCount'));
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

        // Sign the document
        $signedFilename = $this->signingService->sign($signatureRequest);
        if ($signedFilename) {
            $signatureRequest->update(['signed_filename' => $signedFilename]);
        }
        $signatureRequest = SignatureRequest::with(['documentLog.documentType', 'official', 'user'])
            ->find($signatureRequest->id);

        try {
            $user = $signatureRequest->user;
            if ($user) {
                $user->notify(new SignatureApprovedNotification($signatureRequest));
            }
        } catch (\Exception $e) {
            Log::error('Admin approve email failed: ' . $e->getMessage());
            return back()
                ->with('email_warning', 'Email notifikasi gagal dikirim ke pemohon. Gunakan tombol kirim ulang.');
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
            return back()
                ->with('email_warning', 'Email notifikasi gagal dikirim ke pemohon. Gunakan tombol kirim ulang.');
        }

        return back()->with('success', 'Permintaan berhasil ditolak dan notifikasi dikirim ke pemohon.');
    }

    public function resendRequestEmail(SignatureRequest $signatureRequest) {
        $this->authorizeDocumentLog($signatureRequest->documentLog);

        if (!$signatureRequest->isPending()) {
            return back()->with('error', 'Hanya permintaan dengan status menunggu yang bisa dikirim ulang');
        }

        try {
            $signatureRequest->official->notify(new SignatureRequestedNotification($signatureRequest));
            return back()->with('success', 'Email berhasil dikirim ulang ke ' . $signatureRequest->official->staff_name . '.');
        } catch (\Exception $e) {
            Log::error('Resend Request Email Failed: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengirim ulang email: '. $e->getMessage());
        }
    }

    public function resendResultEmail(SignatureRequest $signatureRequest) {
        if (!auth()->user()->isAdmin())
            abort(403);

        if ($signatureRequest->isPending()) {
            return back()->with('error', 'Hasil tinjauan belum ada. Selesaikan tinjauan terlebih dahulu.');
        }

        try {
            $user = $signatureRequest->user;
            if (!$user) {
                return back()->with('error', 'Pengguna tidak ditemukan.');
            }

            if ($signatureRequest->isApproved()) {
                $user->notify(new SignatureApprovedNotification($signatureRequest));
            } else {
                $user->notify(new SignatureRejectedNotification($signatureRequest));
            }

            return back()->with('success', 'Email berhasil dikirim ulang ke ' . $user->name . '.');
        } catch (\Exception $e) {
            Log::error('Resend result email failed: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengirim ulang email: ' . $e->getMessage());
        }
    }

    public function testEmail(Request $request)
    {
        if (!auth()->user()->isAdmin())
            abort(403);

        $request->validate(['test_email' => 'required|email']);

        try {
            \Illuminate\Support\Facades\Mail::raw(
                'Ini adalah email uji koneksi dari sistem eDokPUPRD. Jika Anda menerima email ini, konfigurasi email berjalan dengan baik.',
                function ($message) use ($request) {
                    $message->to($request->test_email)
                        ->subject('[eDokPUPRD] Test Email Koneksi — ' . now()->format('d/m/Y H:i'));
                }
            );
            return back()->with('success', 'Email uji berhasil dikirim ke ' . $request->test_email . '.');
        } catch (\Exception $e) {
            Log::error('Test email failed: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengirim email uji: ' . $e->getMessage());
        }
    }

    // helper

    private function authorizeDocumentLog(DocumentLog $documentLog): void
    {
        $user = auth()->user();
        if (!$user)
            abort(403);
        if (!$user->isAdmin() && $documentLog->user_id !== $user->id)
            abort(403);
    }
}