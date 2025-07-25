// Scripts do jogo extraídos do welcome.blade.php
let map, streetView, currentLocation, userGuess;
let score = 1000;
let attempts = 5;
let round = 1;
let gameActive = true;

const locations = [
    { lat: -22.9068, lng: -43.1729, name: "Cristo Redentor, Rio de Janeiro" }
];

window.initGame = function() {
    initializeMap();
    initializeStreetView();
    setupEventListeners();
    startNewRound();
}

function initializeMap() {
    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 4,
        center: { lat: -14.2350, lng: -51.9253 },
        mapTypeId: 'roadmap'
    });
    map.addListener('click', function(event) {
        if (gameActive) {
            userGuess = {
                lat: event.latLng.lat(),
                lng: event.latLng.lng()
            };
            if (window.userMarker) {
                window.userMarker.setMap(null);
            }
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
    currentLocation = locations[Math.floor(Math.random() * locations.length)];
    streetView.setPosition(currentLocation);
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
        Swal.fire({
            icon: 'warning',
            title: 'Atenção!',
            text: 'Por favor, clique no mapa para fazer seu palpite!',
            confirmButtonColor: '#007bff'
        });
        return;
    }
    const distance = calculateDistance(currentLocation, userGuess);
    attempts--;
    let message = `Distância: ${distance.toFixed(2)} km`;
    let title = 'Resultado do Palpite';
    let icon = 'info';
    if (distance <= 10) {
        title = 'Parabéns! 🎉';
        icon = 'success';
        message += `\n\nVocê acertou! A localização era: ${currentLocation.name}`;
        message += `\n\nPontuação final: ${score} pontos`;
        endRound(true);
    } else {
        score = Math.max(0, score - 200);
        icon = 'error';
        if (attempts > 0) {
            const direction = getDirection(currentLocation, userGuess);
            message += `\n\nDireção: ${direction}`;
            message += `\n\nTentativas restantes: ${attempts}`;
            message += `\n\nPontuação atual: ${score}`;
        } else {
            title = 'Fim de Jogo';
            message += `\n\nSuas tentativas acabaram!`;
            message += `\n\nA localização era: ${currentLocation.name}`;
            message += `\n\nPontuação final: ${score} pontos`;
            endRound(false);
        }
    }
    Swal.fire({
        icon: icon,
        title: title,
        text: message,
        confirmButtonColor: '#007bff',
        allowOutsideClick: false
    });
    updateUI();
}
function calculateDistance(pos1, pos2) {
    const R = 6371;
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
    window.correctMarker = new google.maps.Marker({
        position: currentLocation,
        map: map,
        title: 'Localização correta',
        icon: 'http://maps.google.com/mapfiles/ms/icons/red-dot.png'
    });
    document.getElementById('newGameBtn').style.display = 'inline-block';
    document.getElementById('finalNewGameBtn').style.display = 'inline-block';
    document.getElementById('continueBtn').style.display = 'none';
}
function newGame() {
    score = 1000;
    attempts = 5;
    round++;
    gameActive = true;
    hidePopup();
    startNewRound();
}
window.showHelpMessage = function() {
    const messages = [
        "Ajude-me a encontrar onde estou no mapa! 🗺️",
        "Estou perdido! Você pode me ajudar a descobrir minha localização? 🤔",
        "Olhe ao redor e tente descobrir onde estou! 🔍",
        "Use as pistas visuais para me encontrar no mapa! 👀",
        "Preciso da sua ajuda para descobrir onde estou! 🆘"
    ];
    const randomMessage = messages[Math.floor(Math.random() * messages.length)];
    Swal.fire({
        icon: 'question',
        title: 'Preciso de Ajuda! 🤔',
        text: randomMessage,
        confirmButtonText: 'Vou te ajudar!',
        confirmButtonColor: '#28a745',
        background: '#fff',
        iconColor: '#ffc107'
    });
}
function hidePopup() {
    console.log('hidePopup chamada');
}
function updateUI() {
    document.getElementById('score').textContent = score;
    document.getElementById('attempts').textContent = attempts;
    document.getElementById('round').textContent = round;
}
// window.onload = function() {
//     if (typeof google !== 'undefined') {
//         initGame();
//     }
// };

window.addEventListener("load", () => {
    const waitForGoogle = setInterval(() => {
        if (typeof google !== 'undefined' && typeof google.maps !== 'undefined') {
            clearInterval(waitForGoogle);
            initGame();
        }
    }, 100);
});
