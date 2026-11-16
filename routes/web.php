<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn () => Inertia::render('personajes/index', [
    'message' => 'Inertia funciona',
]))->name('personajes.index');
//Route::get('personajes', [PersonajeController::class, 'index'])->name('personajes.index');
Route::get('personajes/crear', [PersonajeController::class, 'create'])->name('personajes.create');
Route::post('personajes', [PersonajeController::class, 'store'])->name('personajes.store');
Route::get('personajes/{personaje}/editar', [PersonajeController::class, 'edit'])->name('personajes.edit');
Route::put('personajes/{personaje}', [PersonajeController::class, 'update'])->name('personajes.update');
Route::delete('personajes/{personaje}', [PersonajeController::class, 'destroy'])->name('personajes.destroy');
Route::post('personajes/importar', [PersonajeController::class, 'importar'])->name('personajes.importar');