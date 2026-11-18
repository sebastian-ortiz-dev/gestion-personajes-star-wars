<?php

use App\Http\Controllers\PersonajesController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/personajes');
Route::get('/personajes', [PersonajesController::class, 'index'])->name('personajes.index');
Route::get('/personajes/crear', [PersonajesController::class, 'crear'])->name('personajes.crear');
Route::post('/personajes', [PersonajesController::class, 'agregar'])->name('personajes.agregar');
Route::get('/personajes/{id}/editar', [PersonajesController::class, 'editar'])->name('personajes.editar');
Route::put('/personajes/{id}', [PersonajesController::class, 'aplicarCambios'])->name('personajes.aplicarCambios');
Route::delete('/personajes/{id}', [PersonajesController::class, 'borrar'])->name('personajes.borrar');
Route::post('/personajes/importar', [PersonajesController::class, 'importar'])->name('personajes.importar');
