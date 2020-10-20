<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/chat', [App\Http\Controllers\ChatController::class, 'index'])->name('chat.home');

Route::get('/chat/general', [App\Http\Controllers\ChatController::class, 'general'])->name('chat.general');
Route::post('/chat/general/send', [App\Http\Controllers\ChatController::class, 'send_general_message'])->name('chat.general.send');

Route::get('/chat/private/{receiver_id}', [App\Http\Controllers\ChatController::class, 'private_chat'])->name('chat.private')->where(['receiver_id' => '[0-9]+']);
Route::post('/chat/private/{receiver_id}/send', [App\Http\Controllers\ChatController::class, 'send_private_message'])->name('chat.private.send')->where(['receiver_id' => '[0-9]+']);
