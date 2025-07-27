@extends('layouts.app')
@section('content')
<div style="width: 100vw; min-height: 100vh; margin: 0; padding: 0; position: relative; background: #f8f9fa;">
    <div style="position: absolute; top: 80px; right: 32px; z-index: 10;">
        <button id="toggle-info" onclick="toggleInfo()" style="background: #198754; color: #fff; border: none; border-radius: 50%; width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(0,0,0,0.08); cursor: pointer;">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16"><path d="M8 1a7 7 0 1 0 0 14A7 7 0 0 0 8 1zm0 13A6 6 0 1 1 8 2a6 6 0 0 1 0 12zm.93-6.412-1 4.001c-.07.28-.37.411-.647.311-.28-.07-.411-.37-.311-.647l1-4.001A.5.5 0 0 1 8.93 7.588zM8 5a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/></svg>
        </button>
        <div id="info-balloon" style="display:none; position: absolute; top: 60px; right: 0; background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.12); padding: 24px 28px; min-width: 260px; max-width: 340px; text-align: left; z-index: 20;">
            <h3 style="color:#198754; font-weight:700; margin-bottom:10px; font-size:1.2em;">{{ $gincana->nome }}</h3>
            <div style="margin-bottom:8px;"><strong>Contexto:</strong> <span style="color:#333;">{{ $gincana->contexto }}</span></div>
            <div style="margin-bottom:8px;"><strong>Privacidade:</strong> <span style="color:#333;">{{ ucfirst($gincana->privacidade) }}</span></div>
            <div style="margin-bottom:8px;"><strong>Duração:</strong> <span style="color:#333;">{{ $gincana->duracao }} horas</span></div>
            <div style="margin-bottom:8px;"><strong>Criada em:</strong> <span style="color:#333;">{{ $gincana->created_at->format('d/m/Y H:i') }}</span></div>
            <div style="margin-bottom:8px;"><strong>Localização:</strong> <span style="color:#333;">{{ $gincana->latitude }}, {{ $gincana->longitude }}</span></div>
            <button onclick="toggleInfo()" style="margin-top:10px; padding:6px 16px; background:#198754; color:#fff; border:none; border-radius:4px; cursor:pointer; width:100%;">Fechar</button>
        </div>
    </div>
    <div style="position:relative; width: 100vw; height: calc(100vh - 80px);">
        <div id="street-view" style="position:absolute; top:0; left:0; width:100vw; height:100%; border-radius:0; border:none;"></div>
        <div style="position:absolute; top:32px; left:0; width:100vw; display:flex; justify-content:center; z-index:10;">
            <div id="cronometro" style="background:#fff; color:#198754; font-weight:700; font-size:1.15em; padding:8px 16px; border-radius:6px; box-shadow:0 2px 8px rgba(0,0,0,0.08); margin-right:16px;">
                --:--:--
            </div>
            <a href="{{ route('ranking.show', $gincana->id) }}" 
               style="padding: 12px 24px; font-weight: 500; background: #6f42c1; color: white; border: none; border-radius: 6px; font-size: 1.15em; text-decoration: none; margin-right: 12px;">
                🏆 Ver Ranking
            </a>
            <a href="{{ route('gincana.index') }}" class="btn btn-secondary" style="padding: 12px 32px; font-weight: 500; background: #6c757d; color: white; border: none; min-width: 220px; border-radius: 6px; font-size: 1.15em; text-align:center;">Voltar para lista</a>
        </div>
    </div>
</div>
<script>
    function toggleInfo() {
        const balloon = document.getElementById('info-balloon');
        balloon.style.display = balloon.style.display === 'none' || balloon.style.display === '' ? 'block' : 'none';
    }
    // Street View - Locais dinâmicos
    document.addEventListener('DOMContentLoaded', function() {
        const locais = @json($locais);
        let idx = 0;
        let panorama = null;
        function showLocal(i) {
            const lat = parseFloat(locais[i].latitude);
            const lng = parseFloat(locais[i].longitude);
            if (!panorama) {
                panorama = new google.maps.StreetViewPanorama(
                    document.getElementById('street-view'), {
                        position: {lat: lat, lng: lng},
                        pov: { heading: 165, pitch: 0 },
                        zoom: 1
                    }
                );
            } else {
                panorama.setPosition({lat: lat, lng: lng});
            }
        }
        showLocal(idx);
        window.nextLocal = function() {
            idx = Math.floor(Math.random() * locais.length);
            showLocal(idx);
        }
    });
    // Cronômetro
    function startCronometro() {
        const cronometro = document.getElementById('cronometro');
        const createdAt = new Date("{{ $gincana->created_at->format('Y-m-d H:i:s') }}");
        const duracao = {{ $gincana->duracao }} * 60 * 60 * 1000;
        function update() {
            const now = new Date();
            const diff = createdAt.getTime() + duracao - now.getTime();
            if (diff <= 0) {
                cronometro.textContent = 'Expirada';
                cronometro.style.color = '#dc3545';
                return;
            }
            const horas = Math.floor(diff / (1000 * 60 * 60));
            const minutos = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const segundos = Math.floor((diff % (1000 * 60)) / 1000);
            cronometro.textContent = `${String(horas).padStart(2,'0')}:${String(minutos).padStart(2,'0')}:${String(segundos).padStart(2,'0')}`;
            cronometro.style.color = '#198754';
            setTimeout(update, 1000);
        }
        update();
    }
    startCronometro();
</script>
<script>
    function mostrarContexto() {
        document.getElementById('contexto-modal').style.display = 'flex';
    }
    function fecharContexto() {
        document.getElementById('contexto-modal').style.display = 'none';
    }
    // Street View
    document.addEventListener('DOMContentLoaded', function() {
        const lat = parseFloat('{{ $gincana->latitude }}');
        const lng = parseFloat('{{ $gincana->longitude }}');
        const panorama = new google.maps.StreetViewPanorama(
            document.getElementById('street-view'), {
                position: {lat: lat, lng: lng},
                pov: { heading: 165, pitch: 0 },
                zoom: 1
            }
        );
    });
    // Cronômetro
    function startCronometro() {
        const cronometro = document.getElementById('cronometro');
        const createdAt = new Date("{{ $gincana->created_at->format('Y-m-d H:i:s') }}");
        const duracao = {{ $gincana->duracao }} * 60 * 60 * 1000;
        function update() {
            const now = new Date();
            const diff = createdAt.getTime() + duracao - now.getTime();
            if (diff <= 0) {
                cronometro.textContent = 'Expirada';
                cronometro.style.color = '#dc3545';
                return;
            }
            const horas = Math.floor(diff / (1000 * 60 * 60));
            const minutos = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const segundos = Math.floor((diff % (1000 * 60)) / 1000);
            cronometro.textContent = `${String(horas).padStart(2,'0')}:${String(minutos).padStart(2,'0')}:${String(segundos).padStart(2,'0')}`;
            cronometro.style.color = '#198754';
            setTimeout(update, 1000);
        }
        update();
    }
    startCronometro();
</script>
@endsection
