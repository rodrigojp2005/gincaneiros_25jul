<?php

use App\Http\Controllers\GincanaController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RankingController;
use App\Models\GincanaLocal;
use App\Models\Gincana;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SocialiteController;

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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Participacao;
Route::get('/', function (Request $request) {
    $locations = getGameLocations();
    $gincanaId = $request->query('gincana');
    $user = Auth::user();
    if ($gincanaId && $user) {
        $jaJogou = Participacao::where('user_id', $user->id)
            ->where('gincana_id', $gincanaId)
            ->exists();
        if ($jaJogou) {
            return view('gincana.ja_jogada');
        }
        // Destacar a gincana selecionada como primeiro local
        $gincana = App\Models\Gincana::find($gincanaId);
        if ($gincana) {
            $selected = [
                'lat' => (float) $gincana->latitude,
                'lng' => (float) $gincana->longitude,
                'name' => $gincana->nome,
                'gincana_id' => $gincana->id,
                'contexto' => $gincana->contexto
            ];
            $locations = array_filter($locations, function($loc) use ($gincanaId) {
                return $loc['gincana_id'] != $gincanaId;
            });
            array_unshift($locations, $selected);
        }
    }
    return view('welcome', compact('locations'));
})->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::middleware('auth')->group(function () {
    Route::get('/gincana/create', [GincanaController::class, 'create'])->name('gincana.create');
    Route::post('/gincana', [GincanaController::class, 'store'])->name('gincana.store');
    Route::get('/gincana', [GincanaController::class, 'index'])->name('gincana.index');
    Route::get('/gincana/jogadas', [GincanaController::class, 'jogadas'])->name('gincana.jogadas');
    Route::get('/gincana/disponiveis', function() {
        $user = auth()->user();
        $jogadasIds = $user->participacoes()->pluck('gincana_id')->toArray();
        $gincanasDisponiveis = \App\Models\Gincana::where('privacidade', 'publica')
            ->whereNotIn('id', $jogadasIds)
            ->with('user')
            ->get();
        return view('gincana.disponiveis', compact('gincanasDisponiveis'));
    })->name('gincana.disponiveis');
    Route::get('/gincana/{gincana}', [GincanaController::class, 'show'])->name('gincana.show');
    Route::get('/gincana/{gincana}/jogar', [GincanaController::class, 'jogar'])->name('gincana.jogar');
    Route::get('/gincana/{gincana}/edit', [GincanaController::class, 'edit'])->name('gincana.edit');
    Route::put('/gincana/{gincana}', [GincanaController::class, 'update'])->name('gincana.update');
    Route::delete('/gincana/{gincana}', [GincanaController::class, 'destroy'])->name('gincana.destroy');
    
    // Rotas do jogo
    Route::post('/game/save-score', [GameController::class, 'saveScore'])->name('game.save-score');
    
    // Rotas de Ranking
    Route::get('/rankings', [RankingController::class, 'index'])->name('ranking.index');
    Route::get('/ranking/{gincana}', [RankingController::class, 'show'])->name('ranking.show');
    Route::get('/ranking-geral', [RankingController::class, 'geral'])->name('ranking.geral');
});

Route::get('auth/google', [SocialiteController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [SocialiteController::class, 'handleGoogleCallback']);

require __DIR__.'/auth.php';
