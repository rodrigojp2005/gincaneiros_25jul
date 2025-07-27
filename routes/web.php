<?php

use App\Http\Controllers\ProfileController;
use App\Models\GincanaLocal;
use App\Models\Gincana;
use Illuminate\Support\Facades\Route;

// Função para carregar locais de jogo
function getGameLocations() {
    $locations = [];
    
    // 1. Buscar locais principais das gincanas públicas criadas pelos usuários
    $gincanas = Gincana::where('privacidade', 'publica')->get();
    foreach ($gincanas as $gincana) {
        $locations[] = [
            'lat' => (float) $gincana->latitude,
            'lng' => (float) $gincana->longitude,
            'name' => $gincana->nome,
            'gincana_id' => $gincana->id,
            'contexto' => $gincana->contexto
        ];
    }
    
    // 2. Buscar locais adicionais das gincanas públicas (tabela gincana_locais)
    $locaisAdicionais = GincanaLocal::whereHas('gincana', function($query) {
        $query->where('privacidade', 'publica');
    })->with('gincana')->get();
    
    foreach ($locaisAdicionais as $local) {
        $locations[] = [
            'lat' => (float) $local->latitude,
            'lng' => (float) $local->longitude,
            'name' => $local->gincana->nome . ' - Local Adicional',
            'gincana_id' => $local->gincana_id,
            'contexto' => $local->gincana->contexto
        ];
    }
    
    // Se não houver gincanas criadas pelos usuários, usar locais padrão
    if (empty($locations)) {
        $locations = [
            ['lat' => -22.9068, 'lng' => -43.1729, 'name' => 'Cristo Redentor, Rio de Janeiro'] 
        ];
    }
    
    return $locations;
}

// Rota principal - funciona para visitantes e usuários logados
Route::get('/', function () {
    $locations = getGameLocations();
    return view('welcome', compact('locations'));
});

// Rota dashboard - redirecionada para a página principal unificada
Route::get('/dashboard', function () {
    $locations = getGameLocations();
    return view('welcome', compact('locations'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

use App\Http\Controllers\GincanaController;

Route::middleware('auth')->group(function () {
    Route::get('/gincana/create', [GincanaController::class, 'create'])->name('gincana.create');
    Route::post('/gincana', [GincanaController::class, 'store'])->name('gincana.store');
    Route::get('/gincana', [GincanaController::class, 'index'])->name('gincana.index');
    Route::get('/gincana/{gincana}', [GincanaController::class, 'show'])->name('gincana.show');
    Route::get('/gincana/{gincana}/edit', [GincanaController::class, 'edit'])->name('gincana.edit');
    Route::put('/gincana/{gincana}', [GincanaController::class, 'update'])->name('gincana.update');
    Route::delete('/gincana/{gincana}', [GincanaController::class, 'destroy'])->name('gincana.destroy');
});
