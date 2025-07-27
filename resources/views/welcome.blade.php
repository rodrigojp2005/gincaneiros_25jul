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
    <!-- Mensagem de boas-vindas condicional -->
    @guest
        <div class="welcome-banner" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 16px; border-radius: 8px; margin-bottom: 16px; text-align: center;">
            <h2 style="margin: 0 0 8px 0; font-size: 24px; font-weight: bold;">🌍 Bem-vindo ao Gincaneiros!</h2>
            <p style="margin: 0; opacity: 0.9;">Descubra onde você está no mundo usando apenas o Street View. Faça login para salvar seu progresso!</p>
        </div>
    @endguest

    @auth
        <div class="welcome-banner" style="background: linear-gradient(135deg, #48bb78 0%, #38a169 100%); color: white; padding: 12px; border-radius: 8px; margin-bottom: 16px; text-align: center;">
            <h2 style="margin: 0; font-size: 18px; font-weight: bold;">🎮 Olá, {{ auth()->user()->name }}! Pronto para explorar?</h2>
        </div>
    @endauth

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
        @else
            <!-- Call-to-action para visitantes -->
            <div class="guest-actions" style="margin-left: 10px; display: flex; gap: 8px; align-items: center;">
                <div class="guest-message" style="padding: 8px 12px; background-color: #f3f4f6; border-radius: 6px; font-size: 14px; color: #6b7280; border: 1px dashed #d1d5db;">
                    💡 Quer criar suas próprias gincanas?
                </div>
                <a href="{{ route('register') }}" class="btn btn-primary" style="background-color: #3b82f6; font-size: 14px; padding: 8px 16px;">
                    Cadastre-se
                </a>
            </div>
        @endauth
    </div>

    <!-- Slider do mapa -->
    <div id="mapSlider" class="map-slider">
        <button id="closeMapBtn" class="close-btn">✕</button>
        <div id="map" class="map-container"></div>
        <div class="slider-controls">
            <button id="confirmGuessBtn" class="btn btn-success">Confirmar Palpite</button>
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
