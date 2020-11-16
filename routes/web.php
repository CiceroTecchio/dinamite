<?php

use App\Models\Leitura;
use Carbon\Carbon;
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

Route::get('/pontos/perigo', function () {
    $pontos = Leitura::join('equipamentos', 'cod_equipamento', 'equipamentos.id')
        ->where('leituras.created_at', '>=', Carbon::now()->subMinutes(5)->toDateTimeString())
        ->where('leituras.leitura', '>=', DB::raw('equipamentos.leitura_limite'))
        ->select('equipamentos.latitude', 'equipamentos.longitude')
        ->groupBy('leituras.cod_equipamento')
        ->get();

    return $pontos;
});


Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {

    $equipamentos = DB::table('equipamentos')
        ->where('cod_user', Auth::id())
        ->select('id', 'descricao', 'leitura_limite')
        ->get();

    return view('dashboard', compact('equipamentos'));
})->name('dashboard');

Route::get('/leituras/{id}', function ($id) {
    $leituras = Leitura::join('equipamentos', 'cod_equipamento', 'equipamentos.id')
        ->where('equipamentos.id', $id)
        ->select('leituras.*')
        ->get();

    return $leituras;
});

Route::get('/leituras/new/{id}', function ($id) {
    $leituras = Leitura::join('equipamentos', 'cod_equipamento', 'equipamentos.id')
        ->where('equipamentos.id', $id)
        ->select('leituras.*')
        ->orderBy('leituras.id', 'desc')
        ->get()
        ->first();

    return $leituras;
});
