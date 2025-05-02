<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InstituicoesController;
use App\Http\Controllers\ConveniosController;
use App\Http\Controllers\SimularController;

Route::get('/instituicoes', [InstituicoesController::class, 'instituicoes']);
Route::get('/convenios', [ConveniosController::class, 'convenios']);
Route::post('/simular', [SimularController::class, 'simular']);

