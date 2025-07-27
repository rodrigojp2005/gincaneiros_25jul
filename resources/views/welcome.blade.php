@extends('layouts.app')

@section('title')
    @auth
        Dashboard - Gincaneiros
    @else
        Gincaneiros - Jogo de Localização
    @endauth
@endsection

@section('content')
<div class="game-container">
    <!-- Informações do jogo -->
    <div class="game-info">
        <div><strong>Pontuação:</strong> <span id="score">1000</span></div>
        <div><strong>Tentativas:</strong> <span id="attempts">5</span></div>
        <div><strong>Rodada:</strong> <span id="round">1</span></div>
        @auth
            <div><strong>Jogador:</strong> {{ auth()->user()->name }}</div>
        @else
            <div><strong>Modo:</strong> Visitante</div>
        @endauth
    </div>

    <!-- Container do Street View -->
    <div id="streetview" class="street-view-container"></div>

    <!-- Controles do jogo -->
    <div class="game-controls">
        <button id="showMapBtn" class="btn">Ver Mapa</button>
        <button id="newGameBtn" class="btn btn-success" style="display: none;">Novo Jogo</button>
        
        <!-- Controles condicionais baseados no status de autenticação -->
        @auth
            <a href="{{ route('gincana.create') }}" class="btn btn-primary" style="margin-left: 10px; background-color: #6b7280;">
                Criar Gincana
            </a>
            <a href="{{ route('gincana.index') }}" class="btn btn-secondary" style="margin-left: 10px; background-color: #9ca3af;">
                Minhas Gincanas
            </a>
        @endauth
    </div>

    <!-- Slider do mapa -->
    <div id="mapSlider" class="map-slider">
        <!-- Header com título e botão fechar -->
        <div class="map-slider-header">
            <h3 class="map-slider-title">📍 Faça seu Palpite</h3>
            <button id="closeMapBtn" class="close-btn">
                <span>✕</span>
                <span>Fechar</span>
            </button>
        </div>
        
        <!-- Instruções -->
        <div id="mapInstructions" class="map-instructions">
            <span class="map-instructions-icon">👆</span>
            <span>Clique no mapa onde você acha que está!</span>
        </div>
        
        <!-- Container do mapa -->
        <div id="map" class="map-container"></div>
        
        <!-- Footer com controles -->
        <div class="slider-controls">
            <button id="confirmGuessBtn" class="btn btn-success" disabled>
                🎯 Confirmar Palpite
            </button>
            <!-- <button id="cancelGuessBtn" class="btn" style="background-color: #6c757d;">
                ↩️ Voltar
            </button> -->
        </div>
    </div>

    <!-- Popup de feedback -->
    <div id="overlay" class="overlay"></div>
    <div id="popup" class="popup">
        <h3 id="popupTitle">Resultado</h3>
        <p id="popupMessage"></p>
        <button id="continueBtn" class="btn">Continuar</button>
        <button id="finalNewGameBtn" class="btn btn-success" style="display: none;">Novo Jogo</button>
    </div>
</div>

<script>
    // Passar os locais do backend para o JavaScript
    window.gameLocations = @json($locations);
</script>
@endsection
