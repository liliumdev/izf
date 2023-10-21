<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\AnswerController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\QuestionController;
use App\Http\Controllers\Api\PublishAnswerController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/login', [LoginController::class, 'login']);

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('/logout', [LoginController::class, 'logout']);

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::apiResource('questions.answers', AnswerController::class)->only(['index', 'update', 'store']);

    Route::patch('answers/{answer}/publish', PublishAnswerController::class)->name('answers.publish');

    Route::apiResource('categories', CategoryController::class)->only(['update', 'store', 'destroy']);
    Route::apiResource('questions', QuestionController::class)->only(['update']);
    Route::apiResource('tags', TagController::class)->only(['store', 'destroy']);
});

// Non-authenticated users can view categories, list and create questions, and list all tags
Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);
Route::apiResource('questions', QuestionController::class)->only(['index', 'show', 'store']);
Route::apiResource('tags', TagController::class)->only(['index']);

Route::get('/search', SearchController::class)->name('search');
