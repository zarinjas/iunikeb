<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Public\AnsuranCatalogController;
use App\Http\Controllers\Public\AnnouncementController;
use App\Http\Controllers\Public\MembershipApplicationController;
use App\Http\Controllers\Public\FormDirectoryController;
use App\Http\Controllers\Public\MemberVerificationController;
use App\Http\Controllers\Public\FrontpageController;
use App\Http\Controllers\ManifestController;
use Illuminate\Support\Facades\Route;

Route::get('/photo/{path}', function (string $path) {
    if (! str_starts_with($path, 'member-photos/')) {
        abort(404);
    }

    if (! Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
        abort(404);
    }

    return Illuminate\Support\Facades\Storage::disk('public')->response($path, null, [
        'Cache-Control' => 'public, max-age=86400, immutable',
    ]);
})->where('path', '.*')->name('photo.serve');

Route::get('/manifest.json', ManifestController::class)->name('manifest');

Route::get('/', [FrontpageController::class, 'index'])->name('public.home');

Route::get('/membership/apply', [MembershipApplicationController::class, 'create'])->name('public.membership.apply');
Route::post('/membership/apply', [MembershipApplicationController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('public.membership.store');
Route::get('/membership/apply/thank-you/{applicationNo}', [MembershipApplicationController::class, 'thankYou'])->name('public.membership.thank-you');

Route::get('/forms', [FormDirectoryController::class, 'index'])->name('public.forms.index');
Route::get('/forms/category/{category:slug}', [FormDirectoryController::class, 'category'])->name('public.forms.category');
Route::get('/forms/{onlineForm:slug}', [FormDirectoryController::class, 'show'])->name('public.forms.show');
Route::post('/forms/{onlineForm:slug}', [FormDirectoryController::class, 'store'])->name('public.forms.store');
Route::get('/forms/{onlineForm:slug}/submission/{submission}/next-step', [FormDirectoryController::class, 'nextStep'])->name('public.forms.next-step');
Route::post('/forms/{onlineForm:slug}/submission/{submission}/upload-stamped', [FormDirectoryController::class, 'uploadStamped'])->name('public.forms.upload-stamped');
Route::get('/forms/{onlineForm:slug}/submission/{submission}/print', [FormDirectoryController::class, 'printForSubmission'])->name('public.forms.print-submission');

Route::get('/verify/member/{token}', [MemberVerificationController::class, 'show'])->name('public.member-card.verify');

Route::get('/ansuran', [AnsuranCatalogController::class, 'index'])->name('public.ansuran.index');
Route::get('/ansuran/{product:slug}', [AnsuranCatalogController::class, 'show'])->name('public.ansuran.show');

Route::get('/pengumuman', [AnnouncementController::class, 'index'])->name('public.announcements.index');
Route::get('/pengumuman/{slug}', [AnnouncementController::class, 'show'])->name('public.announcements.show');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::fallback(function () {
    return inertia('Public/Pages/Dummy', [
        'title' => 'Halaman',
        'description' => 'Halaman ini akan dibangunkan tidak lama lagi.',
    ]);
});
