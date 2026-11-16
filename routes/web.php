<?php

use App\Http\Controllers\PersonajesController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::redirect('/', '/personajes');
Route::get('/personajes', [PersonajesController::class, 'index'])->name('personajes.index');
Route::get('/personajes/crear', [PersonajesController::class, 'create'])->name('personajes.create');
Route::post('/personajes', [PersonajesController::class, 'store'])->name('personajes.store');
Route::get('/personajes/{id}/editar', [PersonajesController::class, 'edit'])->name('personajes.edit');
Route::put('/personajes/{id}', [PersonajesController::class, 'update'])->name('personajes.update');
Route::delete('/personajes/{id}', [PersonajesController::class, 'destroy'])->name('personajes.destroy');
Route::post('/personajes/importar', [PersonajesController::class, 'importar'])->name('personajes.importar');