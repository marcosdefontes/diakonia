<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
Route::group(['middleware' => 'web'], function () {
    Route::get('/', 'HomeController@welcome');
    Route::auth();
});


/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web','auth']], function () {


    Route::get('/home', 'HomeController@index')
        ->name('home');

    Route::match(['put','patch'],'/retiros/grupos/ativacao/{grupo}'
        ,'GrupoInscricaoController@ativacao');
    Route::get('/retiros/grupos','GrupoInscricaoController@index');
    Route::post('/retiros/grupos','GrupoInscricaoController@salvar');

    Route::get('/regioes','RegiaoController@index');

    Route::resource('local','LocalController');

});

Route::group(['middleware' => 'web','as'=>'material.','prefix'=>'material'], function () {
    Route::resource('ensino','material\EnsinoController');
});
