<?php
use App\Http\Controllers\ContactListController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SDMController;
use App\Http\Controllers\CatatanSDMController;
use App\Http\Controllers\ReportingController;
use App\Http\Controllers\TrainingController;
use App\Http\Middleware\TrainingProgress;
use App\Http\Middleware\SessionExpiredMiddleware;
use App\Http\Controllers\TrainingBranchController;

Auth::routes();

Route::get('/', function () {
    return view('auth.login');
});

// Route::get('/dashboard', function () {
//     return view('admin.dashboard');
// })->middleware(['auth'])->name('dashboard');


Route::middleware([SessionExpiredMiddleware::class])->group(function () {
Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

Route::get('/admin/target-settings', [HomeController::class, 'indexTarget'])->name('target.index');
Route::post('/admin/target-settings', [HomeController::class, 'update'])->name('target.update');


Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/contact-list', [ContactListController::class, 'index'])->name('contactlist.index');
Route::get('/contacts/export', [ContactListController::class, 'exportContactExcel'])->name('contact-list.export');
Route::post('/data-contact/store', [ContactListController::class, 'store'])->name('contact.store');
Route::get('/data-contact/edit/{id}', [ContactListController::class, 'edit'])->name('contact.edit');
Route::patch('/data-contact/update/{id}', [ContactListController::class, 'update'])->name('contact.update');
Route::delete('/data-contact/delete/{id}', [ContactListController::class, 'destroy'])->name('contact.destroy');

Route::get('/contacts/history/{id}', [ContactListController::class, 'history'])->name('contacts.history');
Route::get('/contacts/{id}', [ContactListController::class, 'show'])->name('contact.show');
Route::post('/contacts/{id}/upload-picture', [ContactListController::class, 'uploadPicture'])->name('contacts.upload-picture');
Route::post('/contacts/{id}/delete-picture', [ContactListController::class, 'deletePicture'])->name('contacts.delete-picture');
Route::post('/contacts/history/update', [ContactListController::class, 'updateHistory'])->name('contacts.history.update');
Route::delete('/contacts/history/delete/{id}', [ContactListController::class, 'deleteHistory']);
Route::get('/mass-update-group', [AdminController::class, 'massUpdateGroup'])->name('massUpdateGroup');
Route::get('/get-members/{group_id}', [AdminController::class, 'getMembers']);


Route::get('/region-list', [RegionController::class, 'index'])->name('region.index');
Route::post('/region/store', [RegionController::class, 'store'])->name('region.store');
Route::get('/region/edit/{id}', [RegionController::class, 'edit'])->name('region.edit');
Route::post('/region/update/{id}', [RegionController::class, 'update'])->name('region.update');
Route::delete('/region/delete/{id}', [RegionController::class, 'destroy'])->name('region.destroy');

Route::get('/manage-user', [AdminController::class, 'index'])->name('manage-user.index');
Route::post('/admin/update-user-role/{id}', [AdminController::class, 'updateUserRole'])->name('admin.updateUserRole');
Route::post('/admin/update-user-region/{id}', [AdminController::class, 'updateUserRegion'])->name('admin.updateUserRegion');
Route::delete('/admin/delete-user/{id}', [AdminController::class, 'deleteUser'])->name('admin.deleteUser');
Route::get('/admin/getGroupMembers/{groupId}', [AdminController::class, 'getGroupMembers']);

Route::get('/create-sdm', [SDMController::class, 'index'])->name('create-sdm.index');
Route::post('/create-sdm', [SDMController::class, 'store'])->name('create-sdm.store');
Route::get('/show-sdm', [SDMController::class, 'show'])->name('show-sdm.index');
Route::get('/sdm/filter', [SDMController::class, 'filter'])->name('sdm.filter');
Route::get('/view-sdm/{id}', [SDMController::class, 'view'])->name('view-sdm');
Route::get('/edit-sdm/{id}', [SDMController::class, 'edit'])->name('edit-sdm');
Route::delete('/delete-sdm/{id}', [SDMController::class, 'destroy'])->name('delete-sdm');
Route::patch('/update-sdm/{id}', [SDMController::class, 'update'])->name('update-sdm');


Route::get('/manage-group', [AdminController::class, 'indexGroup'])->name('manage-group.index');
Route::post('/store-group', [AdminController::class, 'storeGroup'])->name('admin.storeGroup');
Route::post('/mass-update-history', [AdminController::class, 'massUpdateHistory'])->name('massUpdateHistory');
Route::delete('/delete-group/{id}', [AdminController::class, 'deleteGroup'])->name('admin.deleteGroup');
Route::post('/update-group/{id}', [AdminController::class, 'updateGroup'])->name('admin.updateGroup');

Route::get('/catatan-sdm', [CatatanSDMController::class, 'index'])->name('catatan-sdm.index');
Route::post('/catatan-sdm/store', [CatatanSDMController::class, 'store'])->name('catatan-sdm.store');
Route::get('/catatan-sdm-lists', [CatatanSDMController::class, 'catatanSDMLists'])->name('catatan-sdm.lists');
Route::get('/catatan-sdm/filter', [CatatanSDMController::class, 'filter'])->name('catatan-sdm.filter');
Route::get('/catatan-sdm/export', [CatatanSdmController::class, 'exportExcel'])->name('catatan-sdm.export');
Route::get('/edit-catatan-sdm/{id}', [CatatanSDMController::class, 'edit'])->name('catatan-sdm.edit');
Route::patch('/update-catatan-sdm/{id}', [CatatanSDMController::class, 'update'])->name('catatan-sdm.update');
Route::delete('/delete-catatan-sdm/{id}', [CatatanSDMController::class, 'destroy'])->name('catatan-sdm.destroy');

Route::get('/report-contact', [ReportingController::class, 'index'])->name('report-contact-list.index');
Route::get('/report-filtered-contact', [ReportingController::class, 'showFilteredDataContacts'])->name('report-contact-list.showFilteredDataContacts');
Route::get('/report-skala', [ReportingController::class, 'skalaReport'])->name('report-contact-skala.index');
Route::get('/report-region', [ReportingController::class, 'regionReport'])->name('report-contact-region.index');
Route::get('/report-filtered-region', [ReportingController::class, 'filteredRegionReport'])->name('report-contact-region.filteredRegionReport');
Route::get('/report-filtered-skala', [ReportingController::class, 'filteredSkalaReport'])->name('report-contact-skala.filteredSkalaReport');
Route::get('/sdm-total', [ReportingController::class, 'totalSDM'])->name('report-total-sdm');
Route::get('/report-user-training', [ReportingController::class, 'userTrainingReport'])->name('report-user-training');



Route::post('/report-contact-list/export', [ReportingController::class, 'exportToExcel'])->name('report-contact-list.exportToExcel');
Route::get('/export-skala-report', [ReportingController::class, 'exportSkalaReport'])->name('report-contact-skala.exportSkalaReport');
Route::post('/export-region-report', [ReportingController::class, 'exportRegionReport'])->name('report-contact-region.exportRegionReport');
Route::get('/sdm-report/export', [ReportingController::class, 'exportJumlahSDM'])->name('sdm.report.export');
Route::get('/user-training-report/export', [ReportingController::class, 'exportTraining'])->name('training.report.export');

});


// Authenticated routes group
Route::middleware(['auth'])->group(function () {
    // Edit routes - MUST come before the public {page} route
    Route::get('/training/{branch}/{page}/edit', [TrainingController::class, 'edit'])
         ->name('training.edit');

    Route::put('/training/{page}', [TrainingController::class, 'update']) // Changed URI
         ->name('training.update');

    Route::post('/training/complete-page', [TrainingController::class, 'completePage'])
     ->name('training.complete-page');

    Route::get('/training/chapters', [TrainingController::class, 'selectChapter'])->name('training.chapters');




Route::middleware(['auth'])->group(function () {
    Route::get('/training/create', [TrainingController::class, 'create'])->name('training.create'); // ← First
    Route::post('/training', [TrainingController::class, 'store'])->name('training.store');
    Route::post('/training/reorder', [TrainingController::class, 'reorder'])
     ->name('training.reorder');
    Route::delete('/training/{branch}/{page}', [TrainingController::class, 'destroy'])->name('training.destroy');
    Route::get('/chapters', [TrainingBranchController::class, 'index'])->name('training.chapters.index');
    Route::get('/chapters/create', [TrainingBranchController::class, 'create'])->name('training.chapters.create');
    Route::post('/chapters', [TrainingBranchController::class, 'store'])->name('training.chapters.store');
    Route::get('/chapters/{id}/edit', [TrainingBranchController::class, 'edit'])->name('training.chapters.edit');
    Route::put('/chapters/{id}', [TrainingBranchController::class, 'update'])->name('training.chapters.update');
    Route::delete('/chapters/{id}', [TrainingBranchController::class, 'destroy'])->name('training.chapters.destroy');
    Route::post('/training/start', [TrainingController::class, 'startChapter'])->name('training.start');

});

// Place parameterized routes AFTER static routes
Route::get('/training/{page}', [TrainingController::class, 'show'])->name('training.show');
});