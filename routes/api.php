<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/next-question/{user_token}', [App\Http\Controllers\QuestionController::class, 'getNextQuestion']);
Route::get('/check-answer/{user_token}', [App\Http\Controllers\QuestionController::class, 'checkAnswer']);
Route::get('/results/{user_token}', [App\Http\Controllers\QuestionController::class, 'getGameResults']);
Route::get('/generate-token', [App\Http\Controllers\TokenController::class, 'generateToken']);