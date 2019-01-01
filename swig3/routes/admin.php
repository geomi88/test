<?php

Route::get('adminfront', 'Adminfront@adminindex');
Route::get('/adminwelcome', function () {
    return view('welcome');
});



