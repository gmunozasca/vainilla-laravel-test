<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;

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

Route::get('/download/{token}', function (Request $request, $token) {
    try {
        $data = Crypt::decryptString($token);
        $downloadData = json_decode($data);
        return Storage::download($downloadData->filename_storage, $downloadData->filename);
    } catch (DecryptException $e) {
        abort(response('File not found', 404));
    }
})->name('download');
