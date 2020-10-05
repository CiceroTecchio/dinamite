<?php

use App\Models\Leitura;
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
})->name('inicio');

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    
    $leituras = Leitura::join('equipamentos','cod_equipamento', 'equipamentos.id')
    ->where('equipamentos.cod_user', Auth::id())
    ->select('leituras.*')
    ->get();

    return view('dashboard', compact('leituras'));
})->name('dashboard');
