<?php

namespace App\Http\Controllers;

use App\Models\DocumentLog;
use App\Models\DocumentType;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index() {

        $totalGenerated = DocumentLog::where('status', 'success')->count();
        $totalFailed = DocumentLog::where('status', 'failed')->count();
        $totalUsers = User::count();

        $perType = DocumentType::withCount([
            'documentLogs as success_count' => fn($q) => $q->where('status', 'success'),
            'documentLogs as failed_count' => fn($q) => $q->where('status', 'failed'),
        ])->get();

        $recentLogs = DocumentLog::with(['user', 'documentType'])
            ->latest('generated_at')
            ->take(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalGenerated',
            'totalFailed',
            'totalUsers',
            'perType',
            'recentLogs',
        ));

    }

    public function logs(Request $request) { 

        $query = DocumentLog::with('user', 'documentType')
            ->latest('generated_at');
        
        if ($request->filled('type')) {
            $query->whereHas('documentType', fn($q) =>
                $q->where('key', $request->type));
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $logs = $query->paginate(20)->withQueryString();
        $documentTypes = DocumentType::orderBy('name')->get();
        $users = User::orderBy('name')->get();

        return view('admin.logs', compact('logs', 'documentTypes', 'users'));
    }

    public function users(Request $request) {
        $users = User::withCount('documentLogs')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.users', compact('users'));
    }

    public function updateUserRole(Request $request, User $user) {
        $request->validate([
            'role' => 'required|in:guest,staff,admin',
        ]);
        
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat mengubah role anda sendiri.');
        }

        $user->update(['role' => $request->role]);

        return back()->with('success', "Role {$user->name} berhasil diubah menjadi {$request->role}.");
    }

    public function documentTypes() {
        $documentTypes = DocumentType::withCount('documentLogs')
            ->orderBy('name')
            ->get();

        return view('admin.document-types', compact('documentTypes'));
    }

    public function toggleDocumentType(DocumentType $documentType) {
        $documentType->update(['is_active' => !$documentType->is_active]);
        $status = $documentType->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "{$documentType->name} berhasil {$status}.");
    }
}
