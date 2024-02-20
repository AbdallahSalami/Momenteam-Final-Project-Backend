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



Route::apiResource('roles', RoleController::class); 
Route::apiResource('users', UserController::class);
Route::apiResource('events', EventController::class);
Route::apiResource('memberDetails', MemberDetailController::class);

Route::apiResource('certificates', CertificateController::class);
Route::apiResource('posts', PostController::class);
Route::apiResource('articles', ArticleController::class);


Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);



// Route to send the verification email
Route::post('/email/verification-notification', [VerificationController::class, 'sendVerificationEmail'])->middleware('auth:api');

// Route to handle the email verification link
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verifyEmail'])->middleware('auth:api')->name('verification.verify');



// Route::post('/certificates', [CertificateController::class, 'createCertificate']);
// Route::get('/certificates', [CertificateController::class, 'readCertificates']);
// Route::get('/certificates/{id}', [CertificateController::class, 'readCertificate']);
// Route::patch('/certificates/{id}', [CertificateController::class, 'updateCertificate']);
// Route::delete('/certificates/{id}', [CertificateController::class, 'deleteCertificate']);

