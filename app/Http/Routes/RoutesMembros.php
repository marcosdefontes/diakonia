<?php


Route::group(['middleware' => 'web'], function () {

    //Route::resource('membro/grupo-caseiro','membro\GrupoCaseiroController');
    //
    Route::get('membro/grupo-caseiro','membro\GrupoCaseiroController@index')->name('membros.grupo-caseiro');
    Route::get('membro/grupo-caseiro/lista','membro\GrupoCaseiroController@lista')->name('membros.grupo-caseiro.lista');
    Route::post('membro/grupo-caseiro','membro\GrupoCaseiroController@salvar');

    Route::resource('membro','membro\MembroController');
    Route::post('membro/{membro}/relacionamento/add','membro\RelacionamentoController@addRelacionamento');
    Route::get('membro/{membro}/relacionamentos/{categoria?}', 'membro\RelacionamentoController@relacionamentosMembro');
    Route::get('membros', 'membro\MembroController@lista')->name('membros.lista');
    Route::get('membros/relacionamentos/{categoria?}','membro\RelacionamentoController@relacionamentos');




});
