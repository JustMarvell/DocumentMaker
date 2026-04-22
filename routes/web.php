<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StaffDataController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OfficialDataController;

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
        Route::get('/users', [AdminController::class,'users'])->name('users');
        Route::patch('/users/{user}/role', [AdminController::class,'updateUserRole'])->name('users.updateRole');

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

        Route::get('/staff-data', [AdminController::class,'staffData'])->name('staff-data');
        Route::post('/staff-data', [StaffDataController::class, 'store'])->name('staff-data.store');
        Route::patch('/staff-data/{staffDatum}', [StaffDataController::class, 'update'])->name('staff-data.update');
        Route::delete('/staff-data/{staffDatum}', [StaffDataController::class, 'destroy'])->name('staff-data.destroy');

        Route::get('/official-data', [AdminController::class, 'officialData'])->name('official-data');
        Route::post('/official-data', [OfficialDataController::class, 'store'])->name('official-data.store');
        Route::patch('/official-data/{officialDatum}', [OfficialDataController::class, 'update'])->name('official-data.update');
        Route::delete('/official-data/{officialDatum}', [OfficialDataController::class, 'destroy'])->name('official-data.destroy');
    });

    Route::get('/api/staff', [StaffDataController::class, 'index'])->name('api.staff');
    Route::get('/api/officials', [OfficialDataController::class, 'index'])->name('api.officials');
});

require __DIR__.'/auth.php';
