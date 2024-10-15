<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SchedulerController;
use App\Http\Controllers\ServiceProviderController;
use App\Http\Middleware\CheckAdminUserRole;
use App\Http\Middleware\CheckClientUserRole;
use App\Http\Middleware\CheckSchedulerUserRole;
use App\Http\Middleware\CheckServiceProviderUserRole;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

FacadesAuth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/home/search', [App\Http\Controllers\HomeController::class, 'search'])->name('search');

Route::group(['middleware' => ['auth', 'verified']], function () {
    Route::middleware([CheckAdminUserRole::class])->group(function () {
        Route::post('/scheduler/store', [SchedulerController::class, 'store'])->name('scheduler.store');
        Route::get('/scheduler/view/{id}', [SchedulerController::class, 'viewScheduler'])->name('scheduler.view');
        Route::get('/scheduler/status/{id}', [SchedulerController::class, 'toggleStatus'])->name('scheduler.status');
        Route::get('/scheduler/edit/{id}', [SchedulerController::class, 'editScheduler'])->name('scheduler.edit');
        Route::put('/scheduler/update/{id}', [SchedulerController::class, 'updateScheduler'])->name('scheduler.update');
        Route::delete('/scheduler/delete/{id}', [SchedulerController::class, 'deleteScheduler'])->name('scheduler.delete');
    });
    Route::middleware([CheckSchedulerUserRole::class])->group(function () {
        Route::get('/scheduler/service-provider', [SchedulerController::class, 'serviceProviderIndex'])->name('scheduler.serviceProvider');
        Route::get('/scheduler/service-provider/search', [SchedulerController::class, 'serviceProviderSearch'])->name('scheduler.serviceProvider.search');
        Route::get('/scheduler/service-provider/form1', [SchedulerController::class, 'serviceProviderForm1'])->name('scheduler.serviceProviderForm1');
        Route::post('/scheduler/service-provider/form2', [SchedulerController::class, 'serviceProviderForm2'])->name('scheduler.serviceProviderForm2');
        Route::post('/scheduler/service-provider/store', [SchedulerController::class, 'serviceProviderStore'])->name('scheduler.serviceProvider.store');
        Route::delete('/scheduler/service-provider/delete/{id}', [SchedulerController::class, 'deleteServiceProvider'])->name('scheduler.serviceProvider.delete');
        Route::get('/scheduler/service-provider/status/{id}', [SchedulerController::class, 'toggleServiceProviderStatus'])->name('scheduler.serviceProvider.status');
        Route::get('/scheduler/service-provider/view/{id}', [SchedulerController::class, 'viewServiceProvider'])->name('scheduler.serviceProvider.view');
        Route::get('/scheduler/service-provider/edit/{id}', [SchedulerController::class, 'editServiceProvider'])->name('scheduler.serviceProvider.edit');
        Route::put('/scheduler/service-provider/update/{id}', [SchedulerController::class, 'updateServiceProvider'])->name('scheduler.serviceProvider.update');
        // Route::post('/scheduler/assign-providers', [SchedulerController::class, 'assignProviders'])->name('scheduler.assignProviders');
        Route::post('/scheduler/assign-provider/{request_id}', [SchedulerController::class, 'assignProvider'])->name('scheduler.assignProvider');
        Route::get('/service-request/{request_id}/client/{client_id}', [SchedulerController::class, 'viewRequest'])->name('scheduler.singleRequest.view');
        Route::get('/service-request/{request_id}/', [SchedulerController::class, 'requestNewQuote'])->name('scheduler.requestNewQuote');
        Route::get('/service-request/pass-to-client/{request_id}/', [SchedulerController::class, 'passToClient'])->name('scheduler.passToClient');

        Route::get('/service-categories', [SchedulerController::class, 'serviceCategoriesIndex'])->name('scheduler.serviceCategories');
        Route::post('/service-categories/store', [SchedulerController::class, 'storeServiceCategory'])->name('scheduler.serviceCategories.store');
        Route::patch('/service-categories/status/{serviceCategory}', [SchedulerController::class, 'toggleServiceCategoryStatus'])->name('scheduler.serviceCategories.status');
        Route::delete('/service-categories/delete/{serviceCategory}', [SchedulerController::class, 'deleteServiceCategory'])->name('scheduler.serviceCategories.delete');
        Route::put('/service-categories/update/{serviceCategory}', [SchedulerController::class, 'updateServiceCategories'])->name('scheduler.serviceCategories.update');
    });
    Route::middleware([CheckClientUserRole::class])->group(function () {
        Route::post('/home/requests', [ClientController::class, 'addRequest'])->name('client.addRequest');
        // Route::put('/home/requests/{id}', [ClientController::class, 'updateRequest'])->name('client.updateRequest');
        Route::delete('/request/delete/{id}', [ClientController::class, 'deleteRequest'])->name('client.deleteRequest');
        Route::get('/home/request/{requestId}', [ClientController::class, 'singleRequest'])->name('client.singleRequest.view');
        Route::get('/home/request/new-quote-request/{requestId}/', [ClientController::class, 'requestNewQuote'])->name('client.requestNewQuote');
        Route::get('/home/request/confirm/{requestId}/', [ClientController::class, 'confirm'])->name('client.confirm');
        Route::post('/home/request/reject/{requestId}/', [ClientController::class, 'rejectQuote'])->name('client.rejectQuote');
        Route::get('/home/request/complete/{requestId}/', [ClientController::class, 'completeServiceRequest'])->name('client.completeServiceRequest');
        Route::post('/home/request/rate/{requestId}/', [ClientController::class, 'rateServiceProvider'])->name('client.rateServiceProvider');
        Route::get('/profile', [ClientController::class, 'profileView'])->name('client.profileView');
        Route::get('/profile/edit', [ClientController::class, 'editProfile'])->name('client.editProfile');
        Route::put('/profile/update', [ClientController::class, 'updateProfile'])->name('client.updateProfile');
    });
    Route::middleware([CheckServiceProviderUserRole::class])->group(function () {
        Route::get('/provider/request/{task_id}/client/{client_id}', [ServiceProviderController::class, 'viewRequest'])->name('provider.serviceRequest.view');
        Route::post('/provider/request/{id}', [ServiceProviderController::class, 'rejectRequest'])->name('provider.serviceRequest.reject');
        Route::post('/provider/request/quotation/{serviceRequest}', [ServiceProviderController::class, 'storeQuotation'])->name('provider.storeQuotation');
        Route::put('/provider/quotation/{id}', [ServiceProviderController::class, 'reQuote'])->name('provider.reQuote');
        Route::get('/provider/profile/', [ServiceProviderController::class, 'profileView'])->name('provider.profileView');
        Route::get('/provider/profile/edit', [ServiceProviderController::class, 'editProfile'])->name('provider.editProfile');
        Route::put('/provider/profile/update', [ServiceProviderController::class, 'updateProfile'])->name('provider.updateProfile');
        Route::get('/provider/addService', [ServiceProviderController::class, 'addService'])->name('provider.addService');
        Route::post('/provider/service/store', [ServiceProviderController::class, 'serviceStore'])->name('provider.service.store');
        Route::get('/provider/service/edit/{id}', [ServiceProviderController::class, 'editService'])->name('provider.editService');
        Route::put('/provider/service/update/{id}', [ServiceProviderController::class, 'updateService'])->name('provider.updateService');
        Route::delete('/provider/service/delete/{id}', [ServiceProviderController::class, 'deleteService'])->name('provider.deleteService');
        Route::post('/provider/service/start/{serviceRequest}', [ServiceProviderController::class, 'startService'])->name('provider.startService');
        Route::post('/provider/request/invoice/{serviceRequest}', [ServiceProviderController::class, 'storeInvoice'])->name('provider.storeInvoice');
    });
});
