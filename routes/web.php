<?php

use App\Http\Controllers\PersonajesController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::redirect('/', '/personajes');
Route::get('/personajes', [PersonajesController::class, 'index'])->name('personajes.index');
Route::get('/personajes/crear', [PersonajesController::class, 'crear'])->name('personajes.crear');
Route::post('/personajes', [PersonajesController::class, 'agregar'])->name('personajes.agregar');
Route::get('/personajes/{id}/editar', [PersonajesController::class, 'editar'])->name('personajes.edit');
Route::put('/personajes/{id}', [PersonajesController::class, 'aplicarCambios'])->name('personajes.aplicarCambios');
Route::delete('/personajes/{id}', [PersonajesController::class, 'destroy'])->name('personajes.destroy');
Route::post('/personajes/importar', [PersonajesController::class, 'importar'])->name('personajes.importar');