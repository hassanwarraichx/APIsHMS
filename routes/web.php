<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::post('/test-upload', function (Illuminate\Http\Request $request) {
    if ($request->hasFile('profile_picture')) {
        $path = $request->file('profile_picture')->store('public/profile_picture');
        return response()->json(['stored_path' => $path]);
    }
    return response()->json(['error' => 'No file uploaded']);
});

