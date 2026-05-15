<?php

use App\Http\Controllers\Api\NoteController;

Route::get('/notes', [NoteController::class, 'index']);
Route::post('/notes', [NoteController::class, 'store']);
Route::patch('/notes/{id}/archive', [NoteController::class, 'archive']);
