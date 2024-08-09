<?php

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
    // Route::get('/author-form', [AuthorController::class, 'create'])->name('author.create');
    Route::post('/scheduler/store', [SchedulerController::class, 'store'])->name('scheduler.store');
    // Route::get('/home/scheduler/show', [SchedulerController::class, 'show']);

    // Route::get('/author-form/index', [AuthorController::class, 'index'])->name('author.all');
    // Route::get('author-form/{id}/view', [AuthorController::class, 'view'])->name('author.view');
    // Route::get('/author-form/{id}/edit', [AuthorController::class, 'edit'])->name('author.edit');
    // Route::put('/author-form/{id}', [AuthorController::class, 'update'])->name('author.update');
    // Route::delete('/author-form/{id}', [AuthorController::class, 'delete'])->name('author.delete');
    // Route::get('/author-form/toggle-status/{id}', [AuthorController::class, 'toggleStatus'])->name('author.toggleStatus');

    // Route::get('publication/{id}/user_view', [PublicationController::class, 'userSinglePubView'])->name('publication_user.view');
    // Route::get('publication/{publication}/like', [LikeController::class, 'toggleLike'])->name('publications_user.like');
    // Route::post('publication/{publication}/comments', [CommentController::class, 'storeComment'])->name('publications_user.comments');

    // Route::post('/ratings/{publication}', [RatingController::class, 'store'])->name('rating.store');
});
