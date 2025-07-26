@extends('layouts.app')
@section('content')
<div id="form_container" style="max-width: 600px; margin: 60px auto 0 auto; padding: 20px;">
    <h2 style="margin-bottom: 18px;">Criar Nova Gincana</h2>
    <form id="form-criar-gincana" method="POST" action="{{ route('gincana.store') }}">
        @csrf

        <!-- Nome da Gincana -->
        <div style="margin-bottom: 16px;">
            <label for="nome" style="display: block; font-weight: bold; margin-bottom: 6px;">Nome da Gincana</label>
            <input type="text" id="nome" name="nome" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
        </div>

        <!-- Duração -->
        <div style="margin-bottom: 16px;">
            <label for="duracao" style="display: block; font-weight: bold; margin-bottom: 6px;">Duração</label>
            <select id="duracao" name="duracao" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                <option value="24">24 horas</option>
                <option value="48">48 horas</option>
                <option value="72">72 horas</option>
            </select>
        </div>

        <!-- Mapa do Google Maps -->
        <div style="margin-bottom: 16px; display: flex; gap: 16px;">
            <div style="flex: 2;">
                <label style="display: block; font-weight: bold; margin-bottom: 6px;">Escolha o local do personagem perdido</label>
                <div id="map" style="height: 300px; width: 100%; border: 1px solid #ccc; border-radius: 4px;"></div>
                <input type="hidden" id="latitude" name="latitude">
                <input type="hidden" id="longitude" name="longitude">
            </div>
            <div style="flex: 1;">
                <label style="display: block; font-weight: bold; margin-bottom: 6px;">Street View</label>
                <div id="street-view" style="height: 300px; width: 100%; border: 1px solid #ccc; border-radius: 4px;"></div>
            </div>
        </div

        <!-- Campo de Cidade -->
        <div style="margin-bottom: 16px;">
            <label for="cidade" style="display: block; font-weight: bold; margin-bottom: 6px;">Cidade / Localização</label>
            <div style="display: flex; gap: 8px;">
                <input type="text" id="cidade" placeholder="Digite a cidade ou endereço" style="flex: 1; padding: 8px; border: 1px solid #ccc; border-radius: 4px;" oninput="debouncedBuscarCidade()">
                <button type="button" onclick="buscarCidade()" style="padding: 8px 12px; background-color: #0d6efd; color: white; border: none; border-radius: 4px; cursor: pointer;">
                    Buscar
                </button>
            </div>
            <div id="cidade-feedback" style="margin-top: 8px; color: #198754; font-size: 0.95em;"></div>
        </div>

        <!-- Texto de Contextualização -->
        <div style="margin-bottom: 16px;">
            <label for="contexto" style="display: block; font-weight: bold; margin-bottom: 6px;">Texto de Contextualização (fala do personagem)</label>
            <textarea id="contexto" name="contexto" rows="3" maxlength="255" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;"></textarea>
        </div>

        <!-- Privacidade -->
        <div style="margin-bottom: 16px;">
            <label style="display: block; font-weight: bold; margin-bottom: 6px;">Privacidade</label>
            <div style="margin-bottom: 8px;">
                <input type="radio" name="privacidade" id="publica" value="publica" checked>
                <label for="publica">Pública (qualquer um pode jogar)</label>
            </div>
            <div>
                <input type="radio" name="privacidade" id="privada" value="privada">
                <label for="privada">Privada (apenas quem tem o link pode jogar)</label>
            </div>
        </div>

        <!-- Botões -->
        <div style="display: flex; flex-wrap: wrap; gap: 10px;">
            <button type="submit" style="padding: 10px 20px; background-color: #198754; color: white; border: none; border-radius: 4px; cursor: pointer;">
                Salvar Gincana
            </button>
            <a href="{{ route('gincana.index') }}" style="padding: 10px 20px; background-color: #6c757d; color: white; text-decoration: none; border-radius: 4px; display: inline-block;">
                Cancelar
            </a>
        </div>
    </form>
</div>
@endsection

<!-- Google Maps Script -->
<script>
    let map, marker, geocoder;

    function initMap() {
        const defaultLocation = { lat: -23.55052, lng: -46.633308 }; // São Paulo como exemplo
        map = new google.maps.Map(document.getElementById('map'), {
            center: defaultLocation,
            zoom: 12
        });

        geocoder = new google.maps.Geocoder();

        // Cria o marcador no mapa
        marker = new google.maps.Marker({
            position: defaultLocation,
            map: map,
            draggable: true
        });

        function updateLatLngFields(position) {
            document.getElementById('latitude').value = position.lat();
            document.getElementById('longitude').value = position.lng();
        }

        updateLatLngFields(marker.getPosition());

        marker.addListener('dragend', function() {
            updateLatLngFields(marker.getPosition());
            atualizarStreetView(marker.getPosition());
        });

        map.addListener('click', function(event) {
            marker.setPosition(event.latLng);
            updateLatLngFields(event.latLng);
            atualizarStreetView(event.latLng);
        });
    }

    let buscarCidadeTimeout;
    function debouncedBuscarCidade() {
        clearTimeout(buscarCidadeTimeout);
        buscarCidadeTimeout = setTimeout(buscarCidade, 500);
    }

    function buscarCidade() {
        const endereco = document.getElementById('cidade').value;
        const feedback = document.getElementById('cidade-feedback');
        if (!endereco) {
            feedback.textContent = '';
            return;
        }
        geocoder.geocode({ address: endereco }, function (results, status) {
            if (status === 'OK') {
                const location = results[0].geometry.location;
                // Centraliza o mapa e move o pino
                map.panTo(location);
                map.setZoom(16);
                marker.setPosition(location);
                document.getElementById('latitude').value = location.lat();
                document.getElementById('longitude').value = location.lng();
                feedback.textContent = 'Endereço encontrado: ' + results[0].formatted_address;
                feedback.style.color = '#198754';
                atualizarStreetView(location);
            } else {
                feedback.textContent = 'Local não encontrado.';
                feedback.style.color = '#dc3545';
            }
        });
    }

    function atualizarStreetView(location) {
        const streetViewService = new google.maps.StreetViewService();
        streetViewService.getPanorama({ location: location, radius: 50 }, function(data, svStatus) {
            if (svStatus === 'OK') {
                const panorama = new google.maps.StreetViewPanorama(
                    document.getElementById('street-view'), {
                        position: location,
                        pov: { heading: 165, pitch: 0 },
                        zoom: 1
                    }
                );
            } else {
                document.getElementById('street-view').innerHTML = '<div style="color: #dc3545; padding: 12px;">Este local não possui imagens do Street View!</div>';
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        initMap();
        // Aguarda o mapa e o marcador estarem prontos antes de atualizar o Street View
        setTimeout(function() {
            atualizarStreetView(marker.getPosition());
        }, 500);
    });
</script>
