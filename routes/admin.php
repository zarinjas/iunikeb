<?php

use App\Http\Controllers\Admin\AnsuranAgreementTemplateController;
use App\Http\Controllers\Admin\AnsuranApplicationController;
use App\Http\Controllers\Admin\AnsuranCategoryController;
use App\Http\Controllers\Admin\AnsuranProductController;
use App\Http\Controllers\Admin\AnsuranTenureOptionController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ReviewInboxController;
use App\Http\Controllers\Admin\CarumanController;
use App\Http\Controllers\Admin\ComplaintController;
use App\Http\Controllers\Admin\FinancingApplicationController;
use App\Http\Controllers\Admin\FinancingCategoryController;
use App\Http\Controllers\Admin\Financing\FinancingDocumentTemplateController;
use App\Http\Controllers\Admin\Financing\FinancingSupportingDocumentController;
use App\Http\Controllers\Admin\Financing\FinancingGeneratedDocumentController;
use App\Http\Controllers\Admin\FinancingProductController;
use App\Http\Controllers\Admin\FinancingProductSectionController;
use App\Http\Controllers\Admin\FinancingProductFieldController;
use App\Http\Controllers\Admin\FormCategoryController;
use App\Http\Controllers\Admin\FormFieldController;
use App\Http\Controllers\Admin\FormSectionController;
use App\Http\Controllers\Admin\FormSubmissionController;
use App\Http\Controllers\Admin\FormSubmissionReviewController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\MemberImportController;
use App\Http\Controllers\Admin\MemberSearchController;
use App\Http\Controllers\Admin\MembershipApplicationController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\OnlineFormController;
use App\Http\Controllers\Admin\FrontpageController as AdminFrontpageController;
use App\Http\Controllers\Admin\ProgramController;
use App\Http\Controllers\Admin\ReferralCommissionController;
use App\Http\Controllers\Admin\BrandingController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\AiKnowledgeController;
use App\Http\Controllers\Admin\EmailTemplateController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Support\AccessControl;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'createAdmin'])
        ->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->name('login.store');
    Route::post('/quick-login', [AuthenticatedSessionController::class, 'quickLoginAdmin'])
        ->name('quick-login');
    Route::post('/quick-login/super-admin', [AuthenticatedSessionController::class, 'quickLoginSuperAdmin'])
        ->name('quick-login.super-admin');
    Route::post('/quick-login/member', [AuthenticatedSessionController::class, 'quickLoginMember'])
        ->name('quick-login.member');

    Route::middleware('area:admin')->group(function (): void {
        Route::redirect('/', '/admin/dashboard')->name('home');

        Route::get('/dashboard', DashboardController::class)
            ->middleware('permission:'.AccessControl::PERMISSION_VIEW_ADMIN_DASHBOARD)->name('dashboard');

        Route::get('/semakan', [ReviewInboxController::class, 'index'])
            ->middleware('role:'.AccessControl::ROLE_SUPER_ADMIN.'|'.AccessControl::ROLE_ADMIN)
            ->name('semakan.index');

        // Frontpage CMS
        Route::post('/frontpage/upload-image', [AdminFrontpageController::class, 'uploadImage'])
            ->middleware('permission:'.AccessControl::PERMISSION_EDIT_FRONTPAGE)
            ->name('frontpage.upload-image');
        Route::get('/frontpage', [AdminFrontpageController::class, 'index'])
            ->middleware('permission:'.AccessControl::PERMISSION_EDIT_FRONTPAGE)
            ->name('frontpage.index');
        Route::get('/frontpage/sections/{section:key}', [AdminFrontpageController::class, 'edit'])
            ->middleware('permission:'.AccessControl::PERMISSION_EDIT_FRONTPAGE)
            ->name('frontpage.sections.edit');
        Route::match(['put', 'patch'], '/frontpage/sections/{section:key}', [AdminFrontpageController::class, 'update'])
            ->middleware('permission:'.AccessControl::PERMISSION_EDIT_FRONTPAGE)
            ->name('frontpage.sections.update');
        Route::post('/frontpage/sections/{section:key}/items', [AdminFrontpageController::class, 'storeItem'])
            ->middleware('permission:'.AccessControl::PERMISSION_EDIT_FRONTPAGE)
            ->name('frontpage.sections.items.store');
        Route::match(['put', 'patch'], '/frontpage/sections/{section:key}/items/{item}', [AdminFrontpageController::class, 'updateItem'])
            ->middleware('permission:'.AccessControl::PERMISSION_EDIT_FRONTPAGE)
            ->name('frontpage.sections.items.update');
        Route::delete('/frontpage/sections/{section:key}/items/{item}', [AdminFrontpageController::class, 'destroyItem'])
            ->middleware('permission:'.AccessControl::PERMISSION_EDIT_FRONTPAGE)
            ->name('frontpage.sections.items.destroy');
        Route::post('/frontpage/sections/{section:key}/items/reorder', [AdminFrontpageController::class, 'reorderItems'])
            ->middleware('permission:'.AccessControl::PERMISSION_EDIT_FRONTPAGE)
            ->name('frontpage.sections.items.reorder');

        // Media
        Route::get('/media', [MediaController::class, 'index'])
            ->middleware('permission:'.AccessControl::PERMISSION_VIEW_MEDIA)
            ->name('media.index');
        Route::post('/media', [MediaController::class, 'store'])
            ->middleware('permission:'.AccessControl::PERMISSION_UPLOAD_MEDIA)
            ->name('media.store');
        Route::delete('/media/{media}', [MediaController::class, 'destroy'])
            ->middleware('permission:'.AccessControl::PERMISSION_DELETE_MEDIA)
            ->name('media.destroy');

        // Financing
        Route::get('/financing/categories', [FinancingCategoryController::class, 'index'])
            ->middleware('permission:'.AccessControl::PERMISSION_VIEW_FINANCING)
            ->name('financing.categories.index');
        Route::get('/financing/categories/create', [FinancingCategoryController::class, 'create'])
            ->middleware('permission:'.AccessControl::PERMISSION_MANAGE_FINANCING_CATEGORIES)
            ->name('financing.categories.create');
        Route::post('/financing/categories', [FinancingCategoryController::class, 'store'])
            ->middleware('permission:'.AccessControl::PERMISSION_MANAGE_FINANCING_CATEGORIES)
            ->name('financing.categories.store');
        Route::get('/financing/categories/{category}/edit', [FinancingCategoryController::class, 'edit'])
            ->middleware('permission:'.AccessControl::PERMISSION_VIEW_FINANCING)
            ->name('financing.categories.edit');
        Route::match(['put', 'patch'], '/financing/categories/{category}', [FinancingCategoryController::class, 'update'])
            ->middleware('permission:'.AccessControl::PERMISSION_MANAGE_FINANCING_CATEGORIES)
            ->name('financing.categories.update');

        Route::get('/financing/products', [FinancingProductController::class, 'index'])
            ->middleware('permission:'.AccessControl::PERMISSION_VIEW_FINANCING)
            ->name('financing.products.index');
        Route::get('/financing/products/create', [FinancingProductController::class, 'create'])
            ->middleware('permission:'.AccessControl::PERMISSION_MANAGE_FINANCING_PRODUCTS)
            ->name('financing.products.create');
        Route::post('/financing/products', [FinancingProductController::class, 'store'])
            ->middleware('permission:'.AccessControl::PERMISSION_MANAGE_FINANCING_PRODUCTS)
            ->name('financing.products.store');
        Route::get('/financing/products/{product}/edit', [FinancingProductController::class, 'edit'])
            ->middleware('permission:'.AccessControl::PERMISSION_VIEW_FINANCING)
            ->name('financing.products.edit');
        Route::match(['put', 'patch', 'post'], '/financing/products/{product}', [FinancingProductController::class, 'update'])
            ->middleware('permission:'.AccessControl::PERMISSION_MANAGE_FINANCING_PRODUCTS)
            ->name('financing.products.update');
        Route::delete('/financing/products/{product}', [FinancingProductController::class, 'destroy'])
            ->middleware('permission:'.AccessControl::PERMISSION_MANAGE_FINANCING_PRODUCTS)
            ->name('financing.products.destroy');

        Route::post('/financing/products/{product}/sections', [FinancingProductSectionController::class, 'store'])
            ->middleware('permission:'.AccessControl::PERMISSION_MANAGE_FINANCING_PRODUCTS)
            ->name('financing.products.sections.store');
        Route::match(['put', 'patch'], '/financing/products/{product}/sections/{section}', [FinancingProductSectionController::class, 'update'])
            ->middleware('permission:'.AccessControl::PERMISSION_MANAGE_FINANCING_PRODUCTS)
            ->name('financing.products.sections.update');
        Route::delete('/financing/products/{product}/sections/{section}', [FinancingProductSectionController::class, 'destroy'])
            ->middleware('permission:'.AccessControl::PERMISSION_MANAGE_FINANCING_PRODUCTS)
            ->name('financing.products.sections.destroy');
        Route::post('/financing/products/{product}/sections/{section}/move-up', [FinancingProductSectionController::class, 'moveUp'])
            ->middleware('permission:'.AccessControl::PERMISSION_MANAGE_FINANCING_PRODUCTS)
            ->name('financing.products.sections.move-up');
        Route::post('/financing/products/{product}/sections/{section}/move-down', [FinancingProductSectionController::class, 'moveDown'])
            ->middleware('permission:'.AccessControl::PERMISSION_MANAGE_FINANCING_PRODUCTS)
            ->name('financing.products.sections.move-down');

        Route::post('/financing/products/{product}/fields', [FinancingProductFieldController::class, 'store'])
            ->middleware('permission:'.AccessControl::PERMISSION_MANAGE_FINANCING_PRODUCTS)
            ->name('financing.products.fields.store');
        Route::post('/financing/products/{product}/fields/batch', [FinancingProductFieldController::class, 'batchStore'])
            ->middleware('permission:'.AccessControl::PERMISSION_MANAGE_FINANCING_PRODUCTS)
            ->name('financing.products.fields.batch-store');
        Route::match(['put', 'patch'], '/financing/products/{product}/fields/{field}', [FinancingProductFieldController::class, 'update'])
            ->middleware('permission:'.AccessControl::PERMISSION_MANAGE_FINANCING_PRODUCTS)
            ->name('financing.products.fields.update');
        Route::delete('/financing/products/{product}/fields/{field}', [FinancingProductFieldController::class, 'destroy'])
            ->middleware('permission:'.AccessControl::PERMISSION_MANAGE_FINANCING_PRODUCTS)
            ->name('financing.products.fields.destroy');
        Route::post('/financing/products/{product}/fields/{field}/move-up', [FinancingProductFieldController::class, 'moveUp'])
            ->middleware('permission:'.AccessControl::PERMISSION_MANAGE_FINANCING_PRODUCTS)
            ->name('financing.products.fields.move-up');
        Route::post('/financing/products/{product}/fields/{field}/move-down', [FinancingProductFieldController::class, 'moveDown'])
            ->middleware('permission:'.AccessControl::PERMISSION_MANAGE_FINANCING_PRODUCTS)
            ->name('financing.products.fields.move-down');
        Route::post('/financing/products/{product}/document-templates', [FinancingDocumentTemplateController::class, 'store'])
            ->middleware('permission:'.AccessControl::PERMISSION_MANAGE_FINANCING_PRODUCTS)
            ->name('financing.products.document-templates.store');
        Route::match(['put', 'patch'], '/financing/products/{product}/document-templates/{template}', [FinancingDocumentTemplateController::class, 'update'])
            ->middleware('permission:'.AccessControl::PERMISSION_MANAGE_FINANCING_PRODUCTS)
            ->name('financing.products.document-templates.update');
        Route::delete('/financing/products/{product}/document-templates/{template}', [FinancingDocumentTemplateController::class, 'destroy'])
            ->middleware('permission:'.AccessControl::PERMISSION_MANAGE_FINANCING_PRODUCTS)
            ->name('financing.products.document-templates.destroy');

        Route::post('/financing/products/{product}/supporting-documents', [FinancingSupportingDocumentController::class, 'store'])
            ->middleware('permission:'.AccessControl::PERMISSION_MANAGE_FINANCING_PRODUCTS)
            ->name('financing.products.supporting-documents.store');
        Route::match(['put', 'patch'], '/financing/products/{product}/supporting-documents/{document}', [FinancingSupportingDocumentController::class, 'update'])
            ->middleware('permission:'.AccessControl::PERMISSION_MANAGE_FINANCING_PRODUCTS)
            ->name('financing.products.supporting-documents.update');
        Route::delete('/financing/products/{product}/supporting-documents/{document}', [FinancingSupportingDocumentController::class, 'destroy'])
            ->middleware('permission:'.AccessControl::PERMISSION_MANAGE_FINANCING_PRODUCTS)
            ->name('financing.products.supporting-documents.destroy');

        Route::get('/financing/applications', [FinancingApplicationController::class, 'index'])
            ->middleware('permission:'.AccessControl::PERMISSION_VIEW_FINANCING)
            ->name('financing.applications.index');
        Route::get('/financing/applications/{application}', [FinancingApplicationController::class, 'show'])
            ->middleware('permission:'.AccessControl::PERMISSION_VIEW_FINANCING)
            ->name('financing.applications.show');
        Route::get('/financing/applications/{application}/print', [FinancingApplicationController::class, 'print'])
            ->middleware('permission:'.AccessControl::PERMISSION_VIEW_FINANCING)
            ->name('financing.applications.print');
        Route::get('/financing/applications/{application}/package', [FinancingApplicationController::class, 'package'])
            ->middleware('permission:'.AccessControl::PERMISSION_VIEW_FINANCING)
            ->name('financing.applications.package');
        Route::post('/financing/applications/{application}/in-review', [FinancingApplicationController::class, 'markInReview'])
            ->middleware('permission:'.AccessControl::PERMISSION_REVIEW_FINANCING_APPLICATIONS)
            ->name('financing.applications.in-review');
        Route::post('/financing/applications/{application}/incomplete', [FinancingApplicationController::class, 'markIncomplete'])
            ->middleware('permission:'.AccessControl::PERMISSION_REVIEW_FINANCING_APPLICATIONS)
            ->name('financing.applications.incomplete');
        Route::post('/financing/applications/{application}/approve', [FinancingApplicationController::class, 'approve'])
            ->middleware('permission:'.AccessControl::PERMISSION_APPROVE_FINANCING_APPLICATIONS)
            ->name('financing.applications.approve');
        Route::post('/financing/applications/{application}/reject', [FinancingApplicationController::class, 'reject'])
            ->middleware('permission:'.AccessControl::PERMISSION_APPROVE_FINANCING_APPLICATIONS)
            ->name('financing.applications.reject');
        Route::post('/financing/applications/{application}/cancel', [FinancingApplicationController::class, 'cancel'])
            ->middleware('permission:'.AccessControl::PERMISSION_REVIEW_FINANCING_APPLICATIONS)
            ->name('financing.applications.cancel');
        Route::delete('/financing/applications/{application}', [FinancingApplicationController::class, 'destroy'])
            ->middleware('permission:'.AccessControl::PERMISSION_APPROVE_FINANCING_APPLICATIONS)
            ->name('financing.applications.destroy');
        Route::get('/financing/applications/{application}/documents/{document}/download', [FinancingApplicationController::class, 'downloadDocument'])
            ->middleware('permission:'.AccessControl::PERMISSION_VIEW_FINANCING)
            ->name('financing.applications.documents.download');
        Route::get('/financing/applications/{application}/stamped-form/download', [FinancingApplicationController::class, 'downloadStampedForm'])
            ->middleware('permission:'.AccessControl::PERMISSION_VIEW_FINANCING)
            ->name('financing.applications.stamped-form.download');
        Route::get('/financing/applications/{application}/generated-documents/{document}/download', [FinancingGeneratedDocumentController::class, 'downloadGenerated'])
            ->middleware('permission:'.AccessControl::PERMISSION_VIEW_FINANCING)
            ->name('financing.applications.generated-documents.download');
        Route::get('/financing/applications/{application}/generated-documents/{document}/uploaded', [FinancingGeneratedDocumentController::class, 'downloadUploaded'])
            ->middleware('permission:'.AccessControl::PERMISSION_VIEW_FINANCING)
            ->name('financing.applications.generated-documents.uploaded');
        Route::post('/financing/applications/{application}/generated-documents/{document}/verify', [FinancingGeneratedDocumentController::class, 'verify'])
            ->middleware('permission:'.AccessControl::PERMISSION_REVIEW_FINANCING_APPLICATIONS)
            ->name('financing.applications.generated-documents.verify');
        Route::post('/financing/applications/{application}/generated-documents/{document}/reject', [FinancingGeneratedDocumentController::class, 'reject'])
            ->middleware('permission:'.AccessControl::PERMISSION_REVIEW_FINANCING_APPLICATIONS)
            ->name('financing.applications.generated-documents.reject');

        // Forms
        Route::get('/form-categories', [FormCategoryController::class, 'index'])
            ->middleware('permission:'.AccessControl::PERMISSION_VIEW_FORMS)
            ->name('form-categories.index');
        Route::get('/form-categories/create', [FormCategoryController::class, 'create'])
            ->middleware('permission:'.AccessControl::PERMISSION_CREATE_FORMS)
            ->name('form-categories.create');
        Route::post('/form-categories', [FormCategoryController::class, 'store'])
            ->middleware('permission:'.AccessControl::PERMISSION_CREATE_FORMS)
            ->name('form-categories.store');
        Route::get('/form-categories/{category}/edit', [FormCategoryController::class, 'edit'])
            ->middleware('permission:'.AccessControl::PERMISSION_VIEW_FORMS)
            ->name('form-categories.edit');
        Route::match(['put', 'patch'], '/form-categories/{category}', [FormCategoryController::class, 'update'])
            ->middleware('permission:'.AccessControl::PERMISSION_EDIT_FORMS)
            ->name('form-categories.update');
        Route::post('/form-categories/{category}/toggle', [FormCategoryController::class, 'toggle'])
            ->middleware('permission:'.AccessControl::PERMISSION_EDIT_FORMS)
            ->name('form-categories.toggle');
        Route::delete('/form-categories/{category}', [FormCategoryController::class, 'destroy'])
            ->middleware('permission:'.AccessControl::PERMISSION_DELETE_FORMS)
            ->name('form-categories.destroy');

        Route::get('/forms', [OnlineFormController::class, 'index'])
            ->middleware('permission:'.AccessControl::PERMISSION_VIEW_FORMS)
            ->name('forms.index');
        Route::get('/forms/create', [OnlineFormController::class, 'create'])
            ->middleware('permission:'.AccessControl::PERMISSION_CREATE_FORMS)
            ->name('forms.create');
        Route::post('/forms', [OnlineFormController::class, 'store'])
            ->middleware('permission:'.AccessControl::PERMISSION_CREATE_FORMS)
            ->name('forms.store');
        Route::get('/forms/{onlineForm}/edit', [OnlineFormController::class, 'edit'])
            ->middleware('permission:'.AccessControl::PERMISSION_VIEW_FORMS)
            ->name('forms.edit');
        Route::match(['put', 'patch'], '/forms/{onlineForm}', [OnlineFormController::class, 'update'])
            ->middleware('permission:'.AccessControl::PERMISSION_EDIT_FORMS)
            ->name('forms.update');
        Route::get('/forms/{onlineForm}/preview-pdf', [OnlineFormController::class, 'previewPdf'])
            ->middleware('permission:'.AccessControl::PERMISSION_VIEW_FORMS)
            ->name('forms.preview-pdf');
        Route::post('/forms/{onlineForm}/publish', [OnlineFormController::class, 'publish'])
            ->middleware('permission:'.AccessControl::PERMISSION_PUBLISH_FORMS)
            ->name('forms.publish');
        Route::post('/forms/{onlineForm}/unpublish', [OnlineFormController::class, 'unpublish'])
            ->middleware('permission:'.AccessControl::PERMISSION_PUBLISH_FORMS)
            ->name('forms.unpublish');
        Route::post('/forms/{onlineForm}/archive', [OnlineFormController::class, 'archive'])
            ->middleware('permission:'.AccessControl::PERMISSION_PUBLISH_FORMS)
            ->name('forms.archive');
        Route::delete('/forms/{onlineForm}', [OnlineFormController::class, 'destroy'])
            ->middleware('permission:'.AccessControl::PERMISSION_DELETE_FORMS)
            ->name('forms.destroy');

        Route::get('/forms/{onlineForm}/sections', [FormSectionController::class, 'index'])
            ->middleware('permission:'.AccessControl::PERMISSION_VIEW_FORMS)
            ->name('forms.sections.index');
        Route::post('/forms/{onlineForm}/sections', [FormSectionController::class, 'store'])
            ->middleware('permission:'.AccessControl::PERMISSION_EDIT_FORMS)
            ->name('forms.sections.store');
        Route::post('/forms/{onlineForm}/sections/from-template', [FormSectionController::class, 'storeFromTemplate'])
            ->middleware('permission:'.AccessControl::PERMISSION_EDIT_FORMS)
            ->name('forms.sections.store-from-template');
        Route::match(['put', 'patch'], '/forms/{onlineForm}/sections/{section}', [FormSectionController::class, 'update'])
            ->middleware('permission:'.AccessControl::PERMISSION_EDIT_FORMS)
            ->name('forms.sections.update');
        Route::post('/forms/{onlineForm}/sections/{section}/save-template', [FormSectionController::class, 'saveAsTemplate'])
            ->middleware('permission:'.AccessControl::PERMISSION_EDIT_FORMS)
            ->name('forms.sections.save-template');
        Route::delete('/forms/{onlineForm}/sections/{section}', [FormSectionController::class, 'destroy'])
            ->middleware('permission:'.AccessControl::PERMISSION_EDIT_FORMS)
            ->name('forms.sections.destroy');

        Route::get('/forms/{onlineForm}/fields', [FormFieldController::class, 'index'])
            ->middleware('permission:'.AccessControl::PERMISSION_VIEW_FORMS)
            ->name('forms.fields.index');
        Route::post('/forms/{onlineForm}/fields', [FormFieldController::class, 'store'])
            ->middleware('permission:'.AccessControl::PERMISSION_EDIT_FORMS)
            ->name('forms.fields.store');
        Route::match(['put', 'patch'], '/forms/{onlineForm}/fields/{field}', [FormFieldController::class, 'update'])
            ->middleware('permission:'.AccessControl::PERMISSION_EDIT_FORMS)
            ->name('forms.fields.update');
        Route::delete('/forms/{onlineForm}/fields/{field}', [FormFieldController::class, 'destroy'])
            ->middleware('permission:'.AccessControl::PERMISSION_EDIT_FORMS)
            ->name('forms.fields.destroy');

        Route::get('/forms/{onlineForm}/submissions', [FormSubmissionController::class, 'index'])
            ->middleware('permission:'.AccessControl::PERMISSION_VIEW_FORM_SUBMISSIONS)
            ->name('forms.submissions.index');
        Route::get('/forms/{onlineForm}/submissions/{submission}', [FormSubmissionController::class, 'show'])
            ->middleware('permission:'.AccessControl::PERMISSION_VIEW_FORM_SUBMISSIONS)
            ->name('forms.submissions.show');
        Route::match(['put', 'patch'], '/forms/{onlineForm}/submissions/{submission}', [FormSubmissionController::class, 'update'])
            ->middleware('permission:'.AccessControl::PERMISSION_VIEW_FORM_SUBMISSIONS)
            ->name('forms.submissions.update');
        Route::get('/forms/{onlineForm}/submissions/{submission}/print', [FormSubmissionController::class, 'print'])
            ->middleware('permission:'.AccessControl::PERMISSION_VIEW_FORM_SUBMISSIONS)
            ->name('forms.submissions.print');
        Route::get('/forms/{onlineForm}/submissions/{submission}/files/{file}/download', [FormSubmissionController::class, 'downloadFile'])
            ->middleware('permission:'.AccessControl::PERMISSION_VIEW_FORM_SUBMISSIONS)
            ->name('forms.submissions.files.download');
        Route::get('/forms/{onlineForm}/submissions/{submission}/stamped-file/download', [FormSubmissionController::class, 'downloadStampedFile'])
            ->middleware('permission:'.AccessControl::PERMISSION_VIEW_FORM_SUBMISSIONS)
            ->name('forms.submissions.stamped-file.download');

        Route::get('/form-submissions', [FormSubmissionReviewController::class, 'index'])
            ->middleware('permission:'.AccessControl::PERMISSION_VIEW_FORM_SUBMISSIONS)
            ->name('form-submissions.index');
        Route::get('/form-submissions/{submission}', [FormSubmissionReviewController::class, 'show'])
            ->middleware('permission:'.AccessControl::PERMISSION_VIEW_FORM_SUBMISSIONS)
            ->name('form-submissions.show');
        Route::post('/form-submissions/{submission}/status', [FormSubmissionReviewController::class, 'updateStatus'])
            ->middleware('permission:'.AccessControl::PERMISSION_VIEW_FORM_SUBMISSIONS)
            ->name('form-submissions.update-status');

        // Members
        Route::get('/members', [MemberController::class, 'index'])
            ->middleware('permission:'.AccessControl::PERMISSION_VIEW_MEMBERS)
            ->name('members.index');
        Route::get('/members/create', [MemberController::class, 'create'])
            ->middleware('permission:'.AccessControl::PERMISSION_CREATE_MEMBERS)
            ->name('members.create');
        Route::post('/members', [MemberController::class, 'store'])
            ->middleware('permission:'.AccessControl::PERMISSION_CREATE_MEMBERS)
            ->name('members.store');
        Route::get('/members/import', [MemberImportController::class, 'index'])
            ->middleware('permission:'.AccessControl::PERMISSION_CREATE_MEMBERS)
            ->name('members.import');
        Route::get('/members/import/template', [MemberImportController::class, 'downloadTemplate'])
            ->middleware('permission:'.AccessControl::PERMISSION_CREATE_MEMBERS)
            ->name('members.import.template');
        Route::post('/members/import/preview', [MemberImportController::class, 'preview'])
            ->middleware('permission:'.AccessControl::PERMISSION_CREATE_MEMBERS)
            ->name('members.import.preview');
        Route::post('/members/import', [MemberImportController::class, 'import'])
            ->middleware('permission:'.AccessControl::PERMISSION_CREATE_MEMBERS)
            ->name('members.import.store');
        Route::get('/members/{member}', [MemberController::class, 'show'])
            ->middleware('permission:'.AccessControl::PERMISSION_VIEW_MEMBERS)
            ->name('members.show');
        Route::get('/members/{member}/edit', [MemberController::class, 'edit'])
            ->middleware('permission:'.AccessControl::PERMISSION_EDIT_MEMBERS)
            ->name('members.edit');
        Route::match(['put', 'patch'], '/members/{member}', [MemberController::class, 'update'])
            ->middleware('permission:'.AccessControl::PERMISSION_EDIT_MEMBERS)
            ->name('members.update');
        Route::post('/members/{member}/status', [MemberController::class, 'updateStatus'])
            ->middleware('permission:'.AccessControl::PERMISSION_SUSPEND_MEMBERS)
            ->name('members.status');
        Route::post('/members/{member}/financials', [MemberController::class, 'updateFinancials'])
            ->middleware('permission:'.AccessControl::PERMISSION_EDIT_MEMBER_FINANCIALS)
            ->name('members.financials');

        Route::get('/membership-applications', [MembershipApplicationController::class, 'index'])
            ->middleware('permission:'.AccessControl::PERMISSION_VIEW_MEMBERSHIP_APPLICATIONS)
            ->name('membership-applications.index');
        Route::get('/membership-applications/{application}', [MembershipApplicationController::class, 'show'])
            ->middleware('permission:'.AccessControl::PERMISSION_VIEW_MEMBERSHIP_APPLICATIONS)
            ->name('membership-applications.show');
        Route::post('/membership-applications/{application}/under-review', [MembershipApplicationController::class, 'markUnderReview'])
            ->middleware('permission:'.AccessControl::PERMISSION_REVIEW_MEMBERSHIP_APPLICATIONS)
            ->name('membership-applications.under-review');
        Route::post('/membership-applications/{application}/approve', [MembershipApplicationController::class, 'approve'])
            ->middleware('permission:'.AccessControl::PERMISSION_APPROVE_MEMBERSHIP_APPLICATIONS)
            ->name('membership-applications.approve');
        Route::post('/membership-applications/{application}/reject', [MembershipApplicationController::class, 'reject'])
            ->middleware('permission:'.AccessControl::PERMISSION_REJECT_MEMBERSHIP_APPLICATIONS)
            ->name('membership-applications.reject');
        Route::post('/membership-applications/{application}/cancel', [MembershipApplicationController::class, 'cancel'])
            ->middleware('permission:'.AccessControl::PERMISSION_REVIEW_MEMBERSHIP_APPLICATIONS)
            ->name('membership-applications.cancel');
        Route::post('/membership-applications/{application}/send-approval-email', [MembershipApplicationController::class, 'sendApprovalEmail'])
            ->middleware('permission:'.AccessControl::PERMISSION_APPROVE_MEMBERSHIP_APPLICATIONS)
            ->name('membership-applications.send-approval-email');

        // Programs
        Route::get('/programs', [ProgramController::class, 'index'])
            ->middleware('permission:'.AccessControl::PERMISSION_VIEW_PROGRAMS)
            ->name('programs.index');
        Route::get('/programs/create', [ProgramController::class, 'create'])
            ->middleware('permission:'.AccessControl::PERMISSION_CREATE_PROGRAMS)
            ->name('programs.create');
        Route::post('/programs', [ProgramController::class, 'store'])
            ->middleware('permission:'.AccessControl::PERMISSION_CREATE_PROGRAMS)
            ->name('programs.store');
        Route::get('/programs/{program}/edit', [ProgramController::class, 'edit'])
            ->middleware('permission:'.AccessControl::PERMISSION_VIEW_PROGRAMS)
            ->name('programs.edit');
        Route::match(['put', 'patch'], '/programs/{program}', [ProgramController::class, 'update'])
            ->middleware('permission:'.AccessControl::PERMISSION_EDIT_PROGRAMS)
            ->name('programs.update');
        Route::get('/programs/{program}', [ProgramController::class, 'show'])
            ->middleware('permission:'.AccessControl::PERMISSION_VIEW_PROGRAMS)
            ->name('programs.show');
        Route::post('/programs/{program}/publish', [ProgramController::class, 'publish'])
            ->middleware('permission:'.AccessControl::PERMISSION_PUBLISH_PROGRAMS)
            ->name('programs.publish');
        Route::post('/programs/{program}/cancel', [ProgramController::class, 'cancel'])
            ->middleware('permission:'.AccessControl::PERMISSION_PUBLISH_PROGRAMS)
            ->name('programs.cancel');
        Route::post('/programs/{program}/complete', [ProgramController::class, 'complete'])
            ->middleware('permission:'.AccessControl::PERMISSION_PUBLISH_PROGRAMS)
            ->name('programs.complete');
        Route::delete('/programs/{program}', [ProgramController::class, 'destroy'])
            ->middleware('permission:'.AccessControl::PERMISSION_DELETE_PROGRAMS)
            ->name('programs.destroy');
        Route::get('/programs/{program}/attendance', [ProgramController::class, 'attendance'])
            ->middleware('permission:'.AccessControl::PERMISSION_VIEW_ATTENDANCE_REPORTS)
            ->name('programs.attendance');
        Route::post('/programs/{program}/attendance/scan', [ProgramController::class, 'scanMember'])
            ->middleware('permission:'.AccessControl::PERMISSION_SCAN_ATTENDANCE)
            ->name('programs.attendance.scan');
        Route::post('/programs/{program}/attendance/scan-qr', [ProgramController::class, 'scanQr'])
            ->middleware('permission:'.AccessControl::PERMISSION_SCAN_ATTENDANCE)
            ->name('programs.attendance.scan-qr');
        Route::post('/programs/{program}/attendance/manual', [ProgramController::class, 'manualAttendance'])
            ->middleware('permission:'.AccessControl::PERMISSION_SCAN_ATTENDANCE)
            ->name('programs.attendance.manual');
        Route::get('/programs/{program}/event-qr', [ProgramController::class, 'eventQr'])
            ->middleware('permission:'.AccessControl::PERMISSION_VIEW_ATTENDANCE_REPORTS)
            ->name('programs.event-qr');

        // Referral Commissions
        Route::get('/referral-commissions', [ReferralCommissionController::class, 'index'])
            ->middleware('permission:'.AccessControl::PERMISSION_VIEW_REFERRAL_COMMISSIONS)
            ->name('referral-commissions.index');
        Route::get('/referral-commissions/{commission}', [ReferralCommissionController::class, 'show'])
            ->middleware('permission:'.AccessControl::PERMISSION_VIEW_REFERRAL_COMMISSIONS)
            ->name('referral-commissions.show');
        Route::post('/referral-commissions/{commission}/approve', [ReferralCommissionController::class, 'approve'])
            ->middleware('permission:'.AccessControl::PERMISSION_PROCESS_REFERRAL_PAYMENTS)
            ->name('referral-commissions.approve');
        Route::post('/referral-commissions/{commission}/pay', [ReferralCommissionController::class, 'pay'])
            ->middleware('permission:'.AccessControl::PERMISSION_PROCESS_REFERRAL_PAYMENTS)
            ->name('referral-commissions.pay');
        Route::post('/referral-commissions/{commission}/cancel', [ReferralCommissionController::class, 'cancel'])
            ->middleware('permission:'.AccessControl::PERMISSION_PROCESS_REFERRAL_PAYMENTS)
            ->name('referral-commissions.cancel');
        Route::post('/referral-commissions/settings', [ReferralCommissionController::class, 'updateSettings'])
            ->middleware('permission:'.AccessControl::PERMISSION_EDIT_SETTINGS)
            ->name('referral-commissions.settings');

        // Complaints
        Route::get('/complaints', [ComplaintController::class, 'index'])
            ->middleware('permission:'.AccessControl::PERMISSION_VIEW_COMPLAINTS)
            ->name('complaints.index');
        Route::get('/complaints/{complaint}', [ComplaintController::class, 'show'])
            ->middleware('permission:'.AccessControl::PERMISSION_VIEW_COMPLAINTS)
            ->name('complaints.show');
        Route::match(['put', 'patch'], '/complaints/{complaint}', [ComplaintController::class, 'update'])
            ->middleware('permission:'.AccessControl::PERMISSION_CLOSE_COMPLAINTS)
            ->name('complaints.update');
        Route::post('/complaints/{complaint}/replies', [ComplaintController::class, 'reply'])
            ->middleware('permission:'.AccessControl::PERMISSION_REPLY_COMPLAINTS)
            ->name('complaints.reply');

        // Caruman
        Route::get('/caruman', [CarumanController::class, 'index'])
            ->middleware('permission:'.AccessControl::PERMISSION_VIEW_CARUMAN)
            ->name('caruman.index');
        Route::put('/caruman/{contribution}', [CarumanController::class, 'update'])
            ->middleware('permission:'.AccessControl::PERMISSION_EDIT_CARUMAN)
            ->name('caruman.update');
        Route::post('/caruman', [CarumanController::class, 'storeOrUpdate'])
            ->middleware('permission:'.AccessControl::PERMISSION_EDIT_CARUMAN)
            ->name('caruman.store');

        // Units
        Route::get('/units', [UnitController::class, 'index'])
            ->middleware('permission:'.AccessControl::PERMISSION_MANAGE_UNITS)
            ->name('units.index');
        Route::get('/units/create', [UnitController::class, 'create'])
            ->middleware('permission:'.AccessControl::PERMISSION_MANAGE_UNITS)
            ->name('units.create');
        Route::post('/units', [UnitController::class, 'store'])
            ->middleware('permission:'.AccessControl::PERMISSION_MANAGE_UNITS)
            ->name('units.store');
        Route::get('/units/{unit}/edit', [UnitController::class, 'edit'])
            ->middleware('permission:'.AccessControl::PERMISSION_MANAGE_UNITS)
            ->name('units.edit');
        Route::match(['put', 'patch'], '/units/{unit}', [UnitController::class, 'update'])
            ->middleware('permission:'.AccessControl::PERMISSION_MANAGE_UNITS)
            ->name('units.update');
        Route::delete('/units/{unit}', [UnitController::class, 'destroy'])
            ->middleware('permission:'.AccessControl::PERMISSION_MANAGE_UNITS)
            ->name('units.destroy');

        // Staff
        Route::get('/staff', [StaffController::class, 'index'])
            ->middleware('permission:'.AccessControl::PERMISSION_MANAGE_STAFF)
            ->name('staff.index');
        Route::get('/staff/create', [StaffController::class, 'create'])
            ->middleware('permission:'.AccessControl::PERMISSION_MANAGE_STAFF)
            ->name('staff.create');
        Route::post('/staff', [StaffController::class, 'store'])
            ->middleware('permission:'.AccessControl::PERMISSION_MANAGE_STAFF)
            ->name('staff.store');
        Route::get('/staff/{user}/edit', [StaffController::class, 'edit'])
            ->middleware('permission:'.AccessControl::PERMISSION_MANAGE_STAFF)
            ->name('staff.edit');
        Route::match(['put', 'patch'], '/staff/{user}', [StaffController::class, 'update'])
            ->middleware('permission:'.AccessControl::PERMISSION_MANAGE_STAFF)
            ->name('staff.update');

        Route::get('/roles', fn () => inertia('Admin/Pages/Placeholder', [
            'title' => 'Peranan',
            'description' => 'UI pengurusan peranan tidak termasuk dalam skop Fasa 2.',
        ]))->middleware('permission:'.AccessControl::PERMISSION_VIEW_ROLES)->name('roles.index');

        // Settings
        Route::get('/settings', [SettingsController::class, 'edit'])
            ->middleware('permission:'.AccessControl::PERMISSION_VIEW_SETTINGS)
            ->name('settings.index');
        Route::put('/settings', [SettingsController::class, 'update'])
            ->middleware('permission:'.AccessControl::PERMISSION_EDIT_SETTINGS)
            ->name('settings.update');
        Route::post('/settings/branding/logo', [BrandingController::class, 'uploadLogo'])
            ->middleware('permission:'.AccessControl::PERMISSION_EDIT_SETTINGS)
            ->name('settings.branding.logo');
        Route::post('/settings/branding/favicon', [BrandingController::class, 'uploadFavicon'])
            ->middleware('permission:'.AccessControl::PERMISSION_EDIT_SETTINGS)
            ->name('settings.branding.favicon');

        // Email Templates
        Route::get('/email-templates', [EmailTemplateController::class, 'index'])
            ->middleware('permission:'.AccessControl::PERMISSION_VIEW_SETTINGS)
            ->name('email-templates.index');
        Route::post('/email-templates', [EmailTemplateController::class, 'store'])
            ->middleware('permission:'.AccessControl::PERMISSION_EDIT_SETTINGS)
            ->name('email-templates.store');
        Route::get('/email-templates/{type}/edit', [EmailTemplateController::class, 'edit'])
            ->middleware('permission:'.AccessControl::PERMISSION_VIEW_SETTINGS)
            ->name('email-templates.edit');
        Route::put('/email-templates/{type}', [EmailTemplateController::class, 'update'])
            ->middleware('permission:'.AccessControl::PERMISSION_EDIT_SETTINGS)
            ->name('email-templates.update');
        Route::delete('/email-templates/{type}', [EmailTemplateController::class, 'destroy'])
            ->middleware('permission:'.AccessControl::PERMISSION_EDIT_SETTINGS)
            ->name('email-templates.destroy');

        // Audit Logs
        Route::get('/audit-logs', [AuditLogController::class, 'index'])
            ->middleware('permission:'.AccessControl::PERMISSION_VIEW_AUDIT_LOGS)
            ->name('audit-logs.index');
        Route::get('/audit-logs/{auditLog}', [AuditLogController::class, 'show'])
            ->middleware('permission:'.AccessControl::PERMISSION_VIEW_AUDIT_LOGS)
            ->name('audit-logs.show');

        // Reports
        Route::get('/reports', fn () => inertia('Admin/Pages/Placeholder', [
            'title' => 'Laporan',
            'description' => 'Laporan operasi asas akan dibina selepas modul data utama tersedia.',
        ]))->middleware('permission:'.AccessControl::PERMISSION_VIEW_REPORTS)->name('reports.index');

        Route::get('/members/search', [MemberSearchController::class, 'search'])
            ->middleware('permission:'.AccessControl::PERMISSION_VIEW_MEMBERS)
            ->name('members.search');

        // Ansuran
        Route::get('/ansuran/categories', [AnsuranCategoryController::class, 'index'])
            ->middleware('permission:'.AccessControl::PERMISSION_VIEW_ANSURAN)
            ->name('ansuran.categories.index');
        Route::get('/ansuran/categories/create', [AnsuranCategoryController::class, 'create'])
            ->middleware('permission:'.AccessControl::PERMISSION_MANAGE_ANSURAN_PRODUCTS)
            ->name('ansuran.categories.create');
        Route::post('/ansuran/categories', [AnsuranCategoryController::class, 'store'])
            ->middleware('permission:'.AccessControl::PERMISSION_MANAGE_ANSURAN_PRODUCTS)
            ->name('ansuran.categories.store');
        Route::get('/ansuran/categories/{category}/edit', [AnsuranCategoryController::class, 'edit'])
            ->middleware('permission:'.AccessControl::PERMISSION_VIEW_ANSURAN)
            ->name('ansuran.categories.edit');
        Route::match(['put', 'patch'], '/ansuran/categories/{category}', [AnsuranCategoryController::class, 'update'])
            ->middleware('permission:'.AccessControl::PERMISSION_MANAGE_ANSURAN_PRODUCTS)
            ->name('ansuran.categories.update');
        Route::delete('/ansuran/categories/{category}', [AnsuranCategoryController::class, 'destroy'])
            ->middleware('permission:'.AccessControl::PERMISSION_MANAGE_ANSURAN_PRODUCTS)
            ->name('ansuran.categories.destroy');

        Route::get('/ansuran/products', [AnsuranProductController::class, 'index'])
            ->middleware('permission:'.AccessControl::PERMISSION_VIEW_ANSURAN)
            ->name('ansuran.products.index');
        Route::get('/ansuran/products/create', [AnsuranProductController::class, 'create'])
            ->middleware('permission:'.AccessControl::PERMISSION_MANAGE_ANSURAN_PRODUCTS)
            ->name('ansuran.products.create');
        Route::post('/ansuran/products', [AnsuranProductController::class, 'store'])
            ->middleware('permission:'.AccessControl::PERMISSION_MANAGE_ANSURAN_PRODUCTS)
            ->name('ansuran.products.store');
        Route::get('/ansuran/products/{product}/edit', [AnsuranProductController::class, 'edit'])
            ->middleware('permission:'.AccessControl::PERMISSION_VIEW_ANSURAN)
            ->name('ansuran.products.edit');
        Route::match(['put', 'patch'], '/ansuran/products/{product}', [AnsuranProductController::class, 'update'])
            ->middleware('permission:'.AccessControl::PERMISSION_MANAGE_ANSURAN_PRODUCTS)
            ->name('ansuran.products.update');
        Route::delete('/ansuran/products/{product}', [AnsuranProductController::class, 'destroy'])
            ->middleware('permission:'.AccessControl::PERMISSION_MANAGE_ANSURAN_PRODUCTS)
            ->name('ansuran.products.destroy');

        Route::post('/ansuran/products/{product}/images', [AnsuranProductController::class, 'uploadImage'])
            ->middleware('permission:'.AccessControl::PERMISSION_MANAGE_ANSURAN_PRODUCTS)
            ->name('ansuran.products.images.store');
        Route::delete('/ansuran/products/{product}/images/{image}', [AnsuranProductController::class, 'deleteImage'])
            ->middleware('permission:'.AccessControl::PERMISSION_MANAGE_ANSURAN_PRODUCTS)
            ->name('ansuran.products.images.destroy');
        Route::post('/ansuran/products/{product}/images/{image}/primary', [AnsuranProductController::class, 'setPrimaryImage'])
            ->middleware('permission:'.AccessControl::PERMISSION_MANAGE_ANSURAN_PRODUCTS)
            ->name('ansuran.products.images.primary');

        Route::post('/ansuran/products/{product}/variants', [AnsuranProductController::class, 'storeVariant'])
            ->middleware('permission:'.AccessControl::PERMISSION_MANAGE_ANSURAN_PRODUCTS)
            ->name('ansuran.products.variants.store');
        Route::match(['put', 'patch'], '/ansuran/products/{product}/variants/{variant}', [AnsuranProductController::class, 'updateVariant'])
            ->middleware('permission:'.AccessControl::PERMISSION_MANAGE_ANSURAN_PRODUCTS)
            ->name('ansuran.products.variants.update');
        Route::delete('/ansuran/products/{product}/variants/{variant}', [AnsuranProductController::class, 'destroyVariant'])
            ->middleware('permission:'.AccessControl::PERMISSION_MANAGE_ANSURAN_PRODUCTS)
            ->name('ansuran.products.variants.destroy');

        Route::get('/ansuran/tenures', [AnsuranTenureOptionController::class, 'index'])
            ->middleware('permission:'.AccessControl::PERMISSION_VIEW_ANSURAN)
            ->name('ansuran.tenures.index');
        Route::post('/ansuran/tenures', [AnsuranTenureOptionController::class, 'store'])
            ->middleware('permission:'.AccessControl::PERMISSION_MANAGE_ANSURAN_TENURES)
            ->name('ansuran.tenures.store');
        Route::match(['put', 'patch'], '/ansuran/tenures/{tenure}', [AnsuranTenureOptionController::class, 'update'])
            ->middleware('permission:'.AccessControl::PERMISSION_MANAGE_ANSURAN_TENURES)
            ->name('ansuran.tenures.update');
        Route::post('/ansuran/tenures/{tenure}/toggle', [AnsuranTenureOptionController::class, 'toggle'])
            ->middleware('permission:'.AccessControl::PERMISSION_MANAGE_ANSURAN_TENURES)
            ->name('ansuran.tenures.toggle');
        Route::delete('/ansuran/tenures/{tenure}', [AnsuranTenureOptionController::class, 'destroy'])
            ->middleware('permission:'.AccessControl::PERMISSION_MANAGE_ANSURAN_TENURES)
            ->name('ansuran.tenures.destroy');

        Route::get('/ansuran/templates', [AnsuranAgreementTemplateController::class, 'index'])
            ->middleware('permission:'.AccessControl::PERMISSION_VIEW_ANSURAN)
            ->name('ansuran.templates.index');
        Route::get('/ansuran/templates/create', [AnsuranAgreementTemplateController::class, 'create'])
            ->middleware('permission:'.AccessControl::PERMISSION_MANAGE_ANSURAN_TEMPLATES)
            ->name('ansuran.templates.create');
        Route::post('/ansuran/templates', [AnsuranAgreementTemplateController::class, 'store'])
            ->middleware('permission:'.AccessControl::PERMISSION_MANAGE_ANSURAN_TEMPLATES)
            ->name('ansuran.templates.store');
        Route::get('/ansuran/templates/{template}/edit', [AnsuranAgreementTemplateController::class, 'edit'])
            ->middleware('permission:'.AccessControl::PERMISSION_VIEW_ANSURAN)
            ->name('ansuran.templates.edit');
        Route::match(['put', 'patch'], '/ansuran/templates/{template}', [AnsuranAgreementTemplateController::class, 'update'])
            ->middleware('permission:'.AccessControl::PERMISSION_MANAGE_ANSURAN_TEMPLATES)
            ->name('ansuran.templates.update');
        Route::delete('/ansuran/templates/{template}', [AnsuranAgreementTemplateController::class, 'destroy'])
            ->middleware('permission:'.AccessControl::PERMISSION_MANAGE_ANSURAN_TEMPLATES)
            ->name('ansuran.templates.destroy');

        Route::get('/ansuran/applications', [AnsuranApplicationController::class, 'index'])
            ->middleware('permission:'.AccessControl::PERMISSION_VIEW_ANSURAN)
            ->name('ansuran.applications.index');
        Route::get('/ansuran/applications/{application}', [AnsuranApplicationController::class, 'show'])
            ->middleware('permission:'.AccessControl::PERMISSION_VIEW_ANSURAN)
            ->name('ansuran.applications.show');
        Route::post('/ansuran/applications/{application}/in-review', [AnsuranApplicationController::class, 'markUnderReview'])
            ->middleware('permission:'.AccessControl::PERMISSION_REVIEW_ANSURAN_APPLICATIONS)
            ->name('ansuran.applications.in-review');
        Route::post('/ansuran/applications/{application}/approve', [AnsuranApplicationController::class, 'approve'])
            ->middleware('permission:'.AccessControl::PERMISSION_APPROVE_ANSURAN_APPLICATIONS)
            ->name('ansuran.applications.approve');
        Route::post('/ansuran/applications/{application}/reject', [AnsuranApplicationController::class, 'reject'])
            ->middleware('permission:'.AccessControl::PERMISSION_APPROVE_ANSURAN_APPLICATIONS)
            ->name('ansuran.applications.reject');
        Route::post('/ansuran/applications/{application}/cancel', [AnsuranApplicationController::class, 'cancel'])
            ->middleware('permission:'.AccessControl::PERMISSION_REVIEW_ANSURAN_APPLICATIONS)
            ->name('ansuran.applications.cancel');
        Route::post('/ansuran/applications/{application}/generate-agreement', [AnsuranApplicationController::class, 'generateAgreement'])
            ->middleware('permission:'.AccessControl::PERMISSION_APPROVE_ANSURAN_APPLICATIONS)
            ->name('ansuran.applications.generate-agreement');
        Route::post('/ansuran/applications/{application}/delivery', [AnsuranApplicationController::class, 'updateDelivery'])
            ->middleware('permission:'.AccessControl::PERMISSION_APPROVE_ANSURAN_APPLICATIONS)
            ->name('ansuran.applications.delivery');
        Route::post('/ansuran/applications/{application}/generate-schedule', [AnsuranApplicationController::class, 'generatePaymentSchedule'])
            ->middleware('permission:'.AccessControl::PERMISSION_APPROVE_ANSURAN_APPLICATIONS)
            ->name('ansuran.applications.generate-schedule');
        Route::post('/ansuran/applications/{application}/record-payment', [AnsuranApplicationController::class, 'recordPayment'])
            ->middleware('permission:'.AccessControl::PERMISSION_APPROVE_ANSURAN_APPLICATIONS)
            ->name('ansuran.applications.record-payment');

        // AI Knowledge
        Route::get('/ai-knowledge', [AiKnowledgeController::class, 'index'])
            ->middleware('permission:'.AccessControl::PERMISSION_VIEW_AI_KNOWLEDGE)
            ->name('ai-knowledge.index');
        Route::post('/ai-knowledge', [AiKnowledgeController::class, 'store'])
            ->middleware('permission:'.AccessControl::PERMISSION_MANAGE_AI_KNOWLEDGE)
            ->name('ai-knowledge.store');
        Route::delete('/ai-knowledge/{documentName}', [AiKnowledgeController::class, 'destroy'])
            ->middleware('permission:'.AccessControl::PERMISSION_MANAGE_AI_KNOWLEDGE)
            ->name('ai-knowledge.destroy');

        // Notifications
        Route::get('/notifications', [NotificationController::class, 'index'])
            ->middleware('auth')
            ->name('notifications.index');
        Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])
            ->middleware('auth')
            ->name('notifications.read');
        Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])
            ->middleware('auth')
            ->name('notifications.read-all');
    });
});
