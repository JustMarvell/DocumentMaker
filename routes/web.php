<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StaffDataController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OfficialDataController;
use App\Http\Controllers\SignatureRequestController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

// Route::get('/home', [DocumentController::class, 'index'])->name('home');
// Route::post('/generate', [DocumentController::class, 'generate'])->name('document.generate');

Route::get('/signature/review/{token}', [SignatureRequestController::class, 'review'])->name('signature.review');
Route::post('/signature/review/{token}', [SignatureRequestController::class, 'processReview'])->name('signature.process');

Route::get('/verify/{token}', [SignatureRequestController::class, 'verify'])->name('signature.verify');

Route::middleware('auth')->group(function() {
    Route::get('/dashboard', function(){
        return auth()->user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('home');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class,'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class,'destroy'])->name('profile.destroy');

    // document generator -> all logeged user
    Route::middleware('auth')->group(function(){
        Route::get('/home', [DocumentController::class,'index'])->name('home');
        Route::post('/generate', [DocumentController::class,'generate'])->name('document.generate');
        Route::get('/download/{filename}', [DocumentController::class, 'download'])->name('document.download');
        Route::get('/preview/{filename}', [DocumentController::class, 'preview'])->name('document.preview');

        Route::get('/signature/request/{documentLog}', [SignatureRequestController::class, 'create'])->name('signature.create');
        Route::post('/signature/request/{documentLog}', [SignatureRequestController::class, 'store'])->name('signature.store');
        Route::post('/signature/resend/{signatureRequest}', [SignatureRequestController::class, 'resendRequestEmail'])->name('signature.resend');
    });

    Route::middleware('role:staff,admin')->group(function(){
        // add later
        Route::get('/staff-test', function () {
            return 'You are staff!';
        })->name('staff.test');
    });

    // Admin only
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function() {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

        Route::get('/logs', [AdminController::class,'logs'])->name('logs');
        Route::delete('/logs/bulk-delete', [AdminController::class, 'bulkDeleteLogs'])->name('logs.bulk-delete');

        Route::get('/users', [AdminController::class,'users'])->name('users');
        Route::patch('/users/{user}/role', [AdminController::class,'updateUserRole'])->name('users.updateRole');
        Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');

        Route::get('/document-types', [AdminController::class,'documentTypes'])->name('document-types');
        Route::patch('/document-types/{documentType}/toggle', [AdminController::class,'toggleDocumentType'])->name('document-types.toggle');
        Route::get('/document-types/create', [AdminController::class, 'createDocumentType'])->name('document-types.create');
        Route::post('/document-types', [AdminController::class, 'storeDocumentType'])->name('document-types.store');

        Route::get('/document-types/{documentType}/fields', [AdminController::class, 'manageFields'])->name('document-types.fields');
        Route::post('/document-types/{documentType}/fields', [AdminController::class, 'storeField'])->name('document-types.fields.store');
        Route::patch('/document-types/{documentType}/fields/{field}', [AdminController::class, 'updateField'])->name('document-types.fields.update');
        Route::delete('/document-types/{documentType}/fields/{field}', [AdminController::class, 'destroyField'])->name('document-types.fields.destroy');
        Route::post('/document-types/{documentType}/fields/reorder', [AdminController::class, 'reorderFields'])->name('document-types.fields.reorder');
        Route::get('/document-types/{documentType}/reupload', [AdminController::class, 'reuploadTemplateForm'])->name('document-types.reupload');
        Route::post('/document-types/{documentType}/reupload', [AdminController::class, 'reuploadTemplate'])->name('document-types.reupload.store');
        Route::delete('/document-types/{documentType}', [AdminController::class, 'destroyDocumentType'])->name('document-types.destroy');
        Route::get('/document-types/{documentType}/slots', [AdminController::class, 'manageSlots'])->name('document-types.slots');
        Route::post('/document-types/{documentType}/slots', [AdminController::class, 'storeSlot'])->name('document-types.slots.store');
        Route::delete('/document-types/{documentType}/slots/{slot}', [AdminController::class, 'destroySlot'])->name('document-types.slots.destroy');
        Route::patch('/document-types/{documentType}/toggle-preview', [AdminController::class, 'togglePreview'])->name('document-types.toggle-preview');
        Route::patch('/document-types/{documentType}/toggle-signature', [AdminController::class, 'toggleSignature'])->name('document-types.toggle-signature');
        Route::patch('/document-types/{documentType}/toggle-signature-image', [AdminController::class, 'toggleSignatureImage'])->name('document-types.toggle-signature-image');
        Route::patch('/document-types/{documentType}/toggle-signature-qr', [AdminController::class, 'toggleSignatureQr'])->name('document-types.toggle-signature-qr');
        Route::get('/document-types/{documentType}/number-counter', [AdminController::class, 'numberCounter'])->name('document-types.number-counter');
        Route::post('/document-types/{documentType}/number-counter', [AdminController::class, 'saveNumberCounter'])->name('document-types.number-counter.save');
        Route::patch('/document-types/{documentType}/number-counter/set', [AdminController::class, 'setNumberCounter'])->name('document-types.number-counter.set');
        Route::patch('/document-types/{documentType}/number-counter/reset', [AdminController::class, 'resetNumberCounter'])->name('document-types.number-counter.reset');
        Route::post('/document-types/{documentType}/scan-fields', [AdminController::class,'scanFields'])->name('document-types.fields.scan');
        Route::post('/document-types/{documentType}/fields/bulk-store', [AdminController::class, 'bulkStoreFields'])->name('document-types.fields.bulk-store');

        Route::get('/staff-data', [AdminController::class,'staffData'])->name('staff-data');
        Route::post('/staff-data', [StaffDataController::class, 'store'])->name('staff-data.store');
        Route::patch('/staff-data/{staffDatum}', [StaffDataController::class, 'update'])->name('staff-data.update');
        Route::delete('/staff-data/{staffDatum}', [StaffDataController::class, 'destroy'])->name('staff-data.destroy');

        Route::get('/official-data', [AdminController::class, 'officialData'])->name('official-data');
        Route::post('/official-data', [OfficialDataController::class, 'store'])->name('official-data.store');
        Route::patch('/official-data/{officialDatum}', [OfficialDataController::class, 'update'])->name('official-data.update');
        Route::delete('/official-data/{officialDatum}', [OfficialDataController::class, 'destroy'])->name('official-data.destroy');
        Route::delete('/official-data/{officialDatum}/signature-image', [OfficialDataController::class, 'deleteSignatureImage'])->name('official-data.delete-signature');
        Route::get('/official-data/signature/{filename}', [OfficialDataController::class, 'serveSignature'])->name('official-data.signature');

        Route::get('/guide', [AdminController::class, 'guide'])->name('guide');
        Route::get('/guide/download', [AdminController::class, 'guideDownload'])->name('guide.download');

        Route::get('/signatures', [SignatureRequestController::class, 'adminIndex'])->name('signatures');
        Route::patch('/signatures/{signatureRequest}/approve', [SignatureRequestController::class, 'adminApprove'])->name('signatures.approve');
        Route::patch('/signatures/{signatureRequest}/reject', [SignatureRequestController::class, 'adminReject'])->name('signatures.reject');
        Route::patch('/signatures/{signatureRequest}/resend-result', [SignatureRequestController::class, 'resendResultEmail'])->name('signatures.resend-result');
        
        Route::post('/test-email', [SignatureRequestController::class, 'testEmail'])->name('test-email');

        Route::get('/pdf-settings', [AdminController::class, 'pdfSettings'])->name('pdf-settings');
        Route::post('/pdf-settings', [AdminController::class, 'savePdfSettings'])->name('pdf-settings.save');
        Route::patch('/pdf-settings/reset', [AdminController::class, 'resetPdfCounter'])->name('pdf-settings.reset');
    });

    Route::get('/api/staff', [StaffDataController::class, 'index'])->name('api.staff');
    Route::get('/api/officials', [OfficialDataController::class, 'index'])->name('api.officials');
});

require __DIR__.'/auth.php';
