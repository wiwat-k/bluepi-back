<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, X-Auth-Token, Origin, Authorization');
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', 'Api\ApiController@login');
Route::get('/global-best', 'Api\ApiController@getGlobalBest');
Route::get('/start-game', 'Api\ApiController@startGame');
Route::post('/check-card', 'Api\ApiController@checkCard');
Route::get('/open-card', 'Api\ApiController@openCard');
Route::post('/save-score', 'Api\ApiController@saveScore');
