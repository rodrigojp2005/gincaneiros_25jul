<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Gincaneiros - Jogo de Localização</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Google Maps API -->
        <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBzEzusC_k3oEoPnqynq2N4a0aA3arzH-c&libraries=geometry&callback=initGame"></script>

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            .game-container {
                position: relative;
                width: 100%;
                height: 100vh;
                overflow: hidden;
            }
            
            .street-view-container {
                width: 100%;
                height: 100%;
                position: relative;
            }
            
            .map-slider {
                position: fixed;
                top: 0;
                right: -100%;
                width: 50%;
                height: 100%;
                background: white;
                transition: right 0.3s ease-in-out;
                z-index: 1000;
                box-shadow: -2px 0 10px rgba(0,0,0,0.3);
            }
            
            .map-slider.active {
                right: 0;
            }
            
            .map-container {
                width: 100%;
                height: calc(100% - 120px);
                margin-top: 60px;
            }
            
            .game-controls {
                position: absolute;
                bottom: 20px;
                left: 50%;
                transform: translateX(-50%);
                z-index: 100;
            }
            
            .slider-controls {
                position: absolute;
                bottom: 20px;
                left: 50%;
                transform: translateX(-50%);
                display: flex;
                gap: 10px;
            }
            
            .close-btn {
                position: absolute;
                top: 20px;
                right: 20px;
                background: #ff4444;
                color: white;
                border: none;
                padding: 10px 15px;
                border-radius: 5px;
                cursor: pointer;
            }
            
            .game-info {
                position: absolute;
                top: 20px;
                left: 20px;
                background: rgba(255,255,255,0.9);
                padding: 15px;
                border-radius: 10px;
                z-index: 100;
            }
            
            .popup {
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                background: white;
                padding: 30px;
                border-radius: 10px;
                box-shadow: 0 4px 20px rgba(0,0,0,0.3);
                z-index: 2000;
                display: none;
                text-align: center;
                max-width: 400px;
                width: 90%;
            }
            
            .popup.active {
                display: block;
            }
            
            .overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.5);
                z-index: 1999;
                display: none;
            }
            
            .overlay.active {
                display: block;
            }
            
            .btn {
                background: #007bff;
                color: white;
                border: none;
                padding: 12px 24px;
                border-radius: 5px;
                cursor: pointer;
                font-size: 16px;
                margin: 5px;
            }
            
            .btn:hover {
                background: #0056b3;
            }
            
            .btn-success {
                background: #28a745;
            }
            
            .btn-success:hover {
                background: #1e7e34;
            }
            
            .footer {
                position: fixed;
                bottom: 0;
                left: 0;
                width: 100%;
                background: rgba(0,0,0,0.8);
                color: white;
                text-align: center;
                padding: 10px;
                z-index: 50;
            }
        </style>
    </head>
    <body class="bg-gray-100">
        <!-- Header com menu de login/cadastro -->
        <header class="fixed top-0 left-0 w-full bg-white shadow-md z-50 p-4">
            @if (Route::has('login'))
                <nav class="flex items-center justify-between">
                    <h1 class="text-xl font-bold text-blue-600">Gincaneiros</h1>
                    <div class="flex items-center gap-4">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800">Log in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn">Register</a>
                            @endif
                        @endauth
                    </div>
                </nav>
            @endif
        </header>

        <!-- Container principal do jogo -->
        <div class="game-container" style="margin-top: 80px;">
            <!-- Informações do jogo -->
            <div class="game-info">
                <div><strong>Pontuação:</strong> <span id="score">1000</span></div>
                <div><strong>Tentativas:</strong> <span id="attempts">5</span></div>
                <div><strong>Rodada:</strong> <span id="round">1</span></div>
            </div>

            <!-- Container do Street View -->
            <div id="streetview" class="street-view-container"></div>

            <!-- Controles do jogo -->
            <div class="game-controls">
                <button id="showMapBtn" class="btn">Ver Mapa</button>
                <button id="newGameBtn" class="btn btn-success" style="display: none;">Novo Jogo</button>
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

        <!-- Rodapé temporário -->
        <footer class="footer">
            <p>&copy; 2025 Gincaneiros - Descubra o mundo através de imagens! | Desenvolvido para diversão e aprendizado</p>
        </footer>

        <script>
            // Variáveis globais do jogo
            let map, streetView, currentLocation, userGuess;
            let score = 1000;
            let attempts = 5;
            let round = 1;
            let gameActive = true;

            // Base de localizações (expandir conforme necessário)
            const locations = [
                { lat: -22.9068, lng: -43.1729, name: "Cristo Redentor, Rio de Janeiro" },
                { lat: -23.5505, lng: -46.6333, name: "Centro de São Paulo" },
                { lat: -15.7801, lng: -47.9292, name: "Brasília, DF" },
                { lat: -12.9714, lng: -38.5014, name: "Salvador, BA" },
                { lat: -25.4284, lng: -49.2733, name: "Curitiba, PR" },
                { lat: -19.9167, lng: -43.9345, name: "Belo Horizonte, MG" },
                { lat: -8.0476, lng: -34.8770, name: "Recife, PE" },
                { lat: -3.1190, lng: -60.0217, name: "Manaus, AM" },
                { lat: -27.5954, lng: -48.5480, name: "Florianópolis, SC" },
                { lat: -1.4558, lng: -48.4902, name: "Belém, PA" }
            ];

            function initGame() {
                initializeMap();
                initializeStreetView();
                setupEventListeners();
                startNewRound();
            }

            function initializeMap() {
                map = new google.maps.Map(document.getElementById('map'), {
                    zoom: 6,
                    center: { lat: -14.2350, lng: -51.9253 }, // Centro do Brasil
                    mapTypeId: 'roadmap'
                });

                map.addListener('click', function(event) {
                    if (gameActive) {
                        userGuess = {
                            lat: event.latLng.lat(),
                            lng: event.latLng.lng()
                        };
                        
                        // Remove marcadores anteriores
                        if (window.userMarker) {
                            window.userMarker.setMap(null);
                        }
                        
                        // Adiciona novo marcador
                        window.userMarker = new google.maps.Marker({
                            position: userGuess,
                            map: map,
                            title: 'Seu palpite',
                            icon: 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png'
                        });
                    }
                });
            }

            function initializeStreetView() {
                streetView = new google.maps.StreetViewPanorama(
                    document.getElementById('streetview'),
                    {
                        position: { lat: -22.9068, lng: -43.1729 },
                        pov: { heading: 34, pitch: 10 },
                        zoom: 1,
                        disableDefaultUI: true,
                        showRoadLabels: false
                    }
                );
            }

            function setupEventListeners() {
                document.getElementById('showMapBtn').addEventListener('click', showMap);
                document.getElementById('closeMapBtn').addEventListener('click', hideMap);
                document.getElementById('confirmGuessBtn').addEventListener('click', confirmGuess);
                document.getElementById('continueBtn').addEventListener('click', hidePopup);
                document.getElementById('newGameBtn').addEventListener('click', newGame);
                document.getElementById('finalNewGameBtn').addEventListener('click', newGame);
                
                document.getElementById('overlay').addEventListener('click', hidePopup);
            }

            function startNewRound() {
                // Seleciona localização aleatória
                currentLocation = locations[Math.floor(Math.random() * locations.length)];
                
                // Atualiza Street View
                streetView.setPosition(currentLocation);
                
                // Reset do mapa
                if (window.userMarker) {
                    window.userMarker.setMap(null);
                }
                if (window.correctMarker) {
                    window.correctMarker.setMap(null);
                }
                
                userGuess = null;
                gameActive = true;
                
                updateUI();
            }

            function showMap() {
                document.getElementById('mapSlider').classList.add('active');
            }

            function hideMap() {
                document.getElementById('mapSlider').classList.remove('active');
            }

            function confirmGuess() {
                if (!userGuess) {
                    alert('Por favor, clique no mapa para fazer seu palpite!');
                    return;
                }

                const distance = calculateDistance(currentLocation, userGuess);
                attempts--;
                
                let message = `Distância: ${distance.toFixed(2)} km`;
                let title = 'Resultado do Palpite';
                
                if (distance <= 10) {
                    // Acertou!
                    title = 'Parabéns! 🎉';
                    message += `\n\nVocê acertou! A localização era: ${currentLocation.name}`;
                    message += `\n\nPontuação final: ${score} pontos`;
                    endRound(true);
                } else {
                    // Errou
                    score = Math.max(0, score - 200);
                    
                    if (attempts > 0) {
                        const direction = getDirection(currentLocation, userGuess);
                        message += `\n\nDireção: ${direction}`;
                        message += `\n\nTentativas restantes: ${attempts}`;
                        message += `\n\nPontuação atual: ${score}`;
                    } else {
                        // Acabaram as tentativas
                        title = 'Fim de Jogo';
                        message += `\n\nSuas tentativas acabaram!`;
                        message += `\n\nA localização era: ${currentLocation.name}`;
                        message += `\n\nPontuação final: ${score} pontos`;
                        endRound(false);
                    }
                }
                
                showPopup(title, message);
                hideMap();
                updateUI();
            }

            function calculateDistance(pos1, pos2) {
                const R = 6371; // Raio da Terra em km
                const dLat = (pos2.lat - pos1.lat) * Math.PI / 180;
                const dLng = (pos2.lng - pos1.lng) * Math.PI / 180;
                const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                         Math.cos(pos1.lat * Math.PI / 180) * Math.cos(pos2.lat * Math.PI / 180) *
                         Math.sin(dLng/2) * Math.sin(dLng/2);
                const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
                return R * c;
            }

            function getDirection(target, guess) {
                const dLat = target.lat - guess.lat;
                const dLng = target.lng - guess.lng;
                
                if (Math.abs(dLat) > Math.abs(dLng)) {
                    return dLat > 0 ? 'Norte' : 'Sul';
                } else {
                    return dLng > 0 ? 'Leste' : 'Oeste';
                }
            }

            function endRound(won) {
                gameActive = false;
                
                // Mostra a localização correta no mapa
                window.correctMarker = new google.maps.Marker({
                    position: currentLocation,
                    map: map,
                    title: 'Localização correta',
                    icon: 'http://maps.google.com/mapfiles/ms/icons/red-dot.png'
                });
                
                // Mostra botões de novo jogo
                document.getElementById('newGameBtn').style.display = 'inline-block';
                document.getElementById('finalNewGameBtn').style.display = 'inline-block';
                document.getElementById('continueBtn').style.display = 'none';
            }

            function newGame() {
                score = 1000;
                attempts = 5;
                round++;
                gameActive = true;
                
                document.getElementById('newGameBtn').style.display = 'none';
                document.getElementById('finalNewGameBtn').style.display = 'none';
                document.getElementById('continueBtn').style.display = 'inline-block';
                
                hidePopup();
                startNewRound();
            }

            function showPopup(title, message) {
                document.getElementById('popupTitle').textContent = title;
                document.getElementById('popupMessage').textContent = message;
                document.getElementById('popup').classList.add('active');
                document.getElementById('overlay').classList.add('active');
            }

            function hidePopup() {
                document.getElementById('popup').classList.remove('active');
                document.getElementById('overlay').classList.remove('active');
            }

            function updateUI() {
                document.getElementById('score').textContent = score;
                document.getElementById('attempts').textContent = attempts;
                document.getElementById('round').textContent = round;
            }

            // Inicializa o jogo quando a página carrega
            window.onload = function() {
                if (typeof google !== 'undefined') {
                    initGame();
                }
            };
        </script>
    </body>
</html>
