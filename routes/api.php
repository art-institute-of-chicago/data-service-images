<?php

Route::get('/', function () {
    return redirect('/api/v1');
});

Route::group(['prefix' => 'v1'], function() {

    Route::get('images', 'ImageController@index');
    Route::get('images/{id}', 'ImageController@show');

});
