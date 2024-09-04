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

Route::group(['middleware' => ['auth', 'verified']], function () {
    Route::middleware([CheckAdminUserRole::class])->group(function () {
        Route::post('/scheduler/store', [SchedulerController::class, 'store'])->name('scheduler.store');
    });
    Route::middleware([CheckSchedulerUserRole::class])->group(function () {
        Route::get('/scheduler/service-provider', [SchedulerController::class, 'serviceProviderIndex'])->name('scheduler.serviceProvider');
        Route::get('/scheduler/service-provider/form1', [SchedulerController::class, 'serviceProviderForm1'])->name('scheduler.serviceProviderForm1');
        Route::post('/scheduler/service-provider/form2', [SchedulerController::class, 'serviceProviderForm2'])->name('scheduler.serviceProviderForm2');
        Route::post('/scheduler/service-provider/store', [SchedulerController::class, 'serviceProviderStore'])->name('scheduler.serviceProvider.store');
        // Route::post('/scheduler/assign-providers', [SchedulerController::class, 'assignProviders'])->name('scheduler.assignProviders');
        Route::post('/scheduler/assign-provider/{request_id}', [SchedulerController::class, 'assignProvider'])->name('scheduler.assignProvider');
        Route::get('/service-request/{request_id}/client/{client_id}', [SchedulerController::class, 'viewRequest'])->name('scheduler.singleRequest.view');
        Route::get('/service-request/{request_id}/', [SchedulerController::class, 'requestNewQuote'])->name('scheduler.requestNewQuote');
        Route::get('/service-request/pass-to-client/{request_id}/', [SchedulerController::class, 'passToClient'])->name('scheduler.passToClient');
    });
    Route::middleware([CheckClientUserRole::class])->group(function () {
        Route::post('/home/requests', [ClientController::class, 'addRequest'])->name('client.addRequest');
        // Route::put('/home/requests/{id}', [ClientController::class, 'updateRequest'])->name('client.updateRequest');
        Route::delete('/home/requests/{id}', [ClientController::class, 'deleteRequest'])->name('client.deleteRequest');
        Route::get('/home/request/{requestId}', [ClientController::class, 'singleRequest'])->name('client.singleRequest.view');
        Route::get('/home/request/new-quote-request/{requestId}/', [ClientController::class, 'requestNewQuote'])->name('client.requestNewQuote');
        Route::get('/home/request/confirm/{requestId}/', [ClientController::class, 'confirm'])->name('client.confirm');
        Route::post('/home/request/reject/{requestId}/', [ClientController::class, 'rejectQuote'])->name('client.rejectQuote');
        Route::get('/home/request/complete/{requestId}/', [ClientController::class, 'completeServiceRequest'])->name('client.completeServiceRequest');
        // Route::post('/home/request/rate/{requestId}/', [ClientController::class, 'rateService'])->name('client.rateService');
    });
    Route::middleware([CheckServiceProviderUserRole::class])->group(function () {
        Route::get('/provider/request/{task_id}/client/{client_id}', [ServiceProviderController::class, 'viewRequest'])->name('provider.serviceRequest.view');
        Route::post('/provider/request/{id}', [ServiceProviderController::class, 'rejectRequest'])->name('provider.serviceRequest.reject');
        Route::post('/provider/request/quotation/{serviceRequest}', [ServiceProviderController::class, 'storeQuotation'])->name('provider.storeQuotation');
        Route::put('/provider/quotation/{quotationId}', [ServiceProviderController::class, 'reQuote'])->name('provider.reQuote');
    });
});
