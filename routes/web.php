<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SchedulerController;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

FacadesAuth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::group(['middleware' => ['auth', 'verified']], function () {
    Route::post('/scheduler/store', [SchedulerController::class, 'store'])->name('scheduler.store');

    Route::get('/scheduler/service-provider', [SchedulerController::class, 'serviceProviderIndex'])->name('scheduler.serviceProvider');
    Route::get('/scheduler/service-provider/form1', [SchedulerController::class, 'serviceProviderForm1'])->name('scheduler.serviceProviderForm1');
    Route::post('/scheduler/service-provider/form2', [SchedulerController::class, 'serviceProviderForm2'])->name('scheduler.serviceProviderForm2');
    Route::post('/scheduler/service-provider/store', [SchedulerController::class, 'serviceProviderStore'])->name('scheduler.serviceProvider.store');
    // Route::post('/scheduler/assign-providers', [SchedulerController::class, 'assignProviders'])->name('scheduler.assignProviders');
    Route::post('/scheduler/assign-provider/{request_id}', [SchedulerController::class, 'assignProvider'])->name('scheduler.assignProvider');

    Route::post('/home/requests', [ClientController::class, 'addRequest'])->name('client.addRequest');
    // Route::put('/home/requests/{id}', [ClientController::class, 'updateRequest'])->name('client.updateRequest');
    Route::delete('/home/requests/{id}', [ClientController::class, 'deleteRequest'])->name('client.deleteRequest');



    Route::get('/service-request/{request_id}/client/{client_id}', [SchedulerController::class, 'viewRequest'])->name('scheduler.singleRequest.view');
});
