<?php

namespace App\Http\Controllers;

use App\Models\DocumentLog;
use App\Models\DocumentType;
use App\Models\User;
use App\Models\StaffData;
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

    public function staffData(Request $request) {
        $querry = StaffData::orderBy('staff_name');

        if ($request->filled('search')) {
            $search = $request->search;
            $querry->where(function ($q) use ($search) {
                $q->where('staff_name', 'like', "%{$search}%")
                ->orWhere('nip', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('work_unit')) {
            $querry->where('work_unit', $request->work_unit);
        }

        if ($request->filled('rank')) {
            $querry->where('rank', $request->rank);
        }

        if ($request->filled('position')) {
            $querry->where('position', $request->position);
        }

        $staffList = $querry->paginate(20)->appends($request->querry());

        $workUnits = StaffData::distinct()
            ->pluck('work_unit')
            ->filter(fn($value) => !is_null($value))
            ->sort()
            ->values();

        $ranks = StaffData::whereNotNull('rank')
            ->distinct()
            ->pluck('rank')
            ->sort()
            ->values();
            
        $positions = StaffData::whereNotNull('position')
            ->distinct()
            ->pluck('position')
            ->sort()
            ->values();

        return view('admin.staff-data', compact('staffList', 'workUnits', 'ranks', 'positions'));
    }
}
