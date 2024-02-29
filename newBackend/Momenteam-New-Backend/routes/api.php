<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\MemberDetailController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ArticleController;

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\EventUserController;




Route::apiResource('roles', RoleController::class); 
Route::apiResource('users', UserController::class);
Route::apiResource('memberDetails', MemberDetailController::class);
Route::apiResource('events', EventController::class);

// Add a user to an event
Route::post('/events/add-user', [EventUserController::class, 'addUserToEvent']);
// Remove a user from an event
Route::post('/events/remove-user', [EventUserController::class, 'removeUserFromEvent']);
// Get a user with all events they are registered for
Route::get('/users/{userId}/with-events', [EventUserController::class, 'getUserWithEvents']);
// Get a specific event with its registered users
Route::get('/events/{eventId}/with-users', [EventUserController::class, 'getEventWithUsers']);

Route::apiResource('certificates', CertificateController::class);
Route::apiResource('articles', ArticleController::class);
Route::apiResource('posts', PostController::class);


Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);


// routes/api.php
Route::get('/verify-email/{id}/{hash}', [VerificationController::class, 'verifyEmail'])->name('verification.verify');

// Route to send the verification email
Route::post('/email/verification-notification', [VerificationController::class, 'sendVerificationEmail'])->middleware('auth:api');

// Route to handle the email verification link



// Route::post('/certificates', [CertificateController::class, 'createCertificate']);
// Route::get('/certificates', [CertificateController::class, 'readCertificates']);
// Route::get('/certificates/{id}', [CertificateController::class, 'readCertificate']);
// Route::patch('/certificates/{id}', [CertificateController::class, 'updateCertificate']);
// Route::delete('/certificates/{id}', [CertificateController::class, 'deleteCertificate']);

