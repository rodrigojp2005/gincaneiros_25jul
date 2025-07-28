@extends('layouts.app')

@section('title')
    @auth
        Gincaneiros - Jogue sua gincana!
    @else
        Gincaneiros - Desafio do bem!
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
        <div style="display: flex; justify-content: center; align-items: center; width: 100%; padding: 10px 0;">
            <button id="showMapBtn" class="btn" style="padding: 0; border: none; background: none; width: 100%; max-width: 220px;">
            <img src="https://media0.giphy.com/media/v1.Y2lkPTc5MGI3NjExbjFnOGtlcnl5dmpveGJydTNxb2twNGxudXB3Nm8wMjNlMnI2bDBrZyZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9cw/PrfN2Hqu24ln2eAaOS/giphy.gif" alt="JOGAR" style="width: 100%; height: auto; max-width: 180px; max-height: 120px; display: block; margin: 0 auto;">
            </button>
            @auth
            <a href="{{ route('gincana.create') }}" class="btn" style="padding: 0; border: none; background: none; width: 100%; max-width: 220px; display: block;">
            <img src="https://media0.giphy.com/media/v1.Y2lkPTc5MGI3NjExMjdjcmlsMGNhajk5bDAzdWNpeDhqd3VubnRmczMyZmZ0YW1xNGEwbSZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9cw/OZ9AcS5JsCg9YZfsXO/giphy.gif" alt="CRIAR" style="width: 100%; height: auto; max-width: 80px; max-height: 80px; display: block; margin: 0 auto;">
            </a>
            @endauth
        </div>
        <style>
            @media (max-width: 600px) {
            #showMapBtn img {
                max-width: 100%;
                max-height: 80px;
            }
            .game-controls > div {
                padding: 0 5px;
            }
            }
        </style>
    </div>

    <!-- Slider do mapa -->
    <div id="mapSlider" class="map-slider">
        <div class="map-slider-header">
            <h3 class="map-slider-title">📍 Faça seu Palpite</h3>
            <button id="closeMapBtn" class="close-btn">
                <span>✕</span>
                <span>Fechar</span>
            </button>
        </div>
        <div id="mapInstructions" class="map-instructions">
            <span class="map-instructions-icon">👆</span>
            <span>Clique no mapa onde você acha que está!</span>
        </div>
        <div id="map" class="map-container"></div>
        <div class="slider-controls">
            <button id="confirmGuessBtn" class="btn btn-success" disabled>
                🎯 Confirmar Palpite
            </button>
        </div>
    </div>

    <div id="overlay" class="overlay"></div>
    <div id="popup" class="popup">
        <h3 id="popupTitle">Resultado</h3>
        <p id="popupMessage"></p>
        <button id="continueBtn" class="btn">Continuar</button>
    </div>
</div>

<script>
    // Passar apenas o local da gincana para o JS
    window.gameLocations = [
        {
            lat: {{ $gincana->latitude }},
            lng: {{ $gincana->longitude }},
            name: "{{ $gincana->nome }}",
            gincana_id: {{ $gincana->id }},
            contexto: "{{ $gincana->contexto }}"
        }
    ];
</script>
@endsection
