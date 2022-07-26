<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
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


$method = "event";
Route::get($method.'/index-data',[EventController::class,'index_data'])->name('event.index_data');
// Route::get($method.'/edit-single',[EventController::class,'edit_single'])->name('event.edit_single');
Route::resource($method,EventController::class);

Route::post('/country-list',[EventController::class,'countryList'])->name('countryList');
Route::post('/state-list',[EventController::class,'stateList'])->name('stateList');
Route::post('/city-list',[EventController::class,'cityList'])->name('cityList');
