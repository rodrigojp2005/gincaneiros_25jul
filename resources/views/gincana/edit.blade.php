@extends('layouts.app')
@section('content')
<div id="form_container" style="max-width: 600px; margin: 24px auto 0 auto; padding: 28px 24px 22px 24px; background: #eafaf1; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.07);">
    <h2 style="margin-bottom: 22px; text-align: center; font-weight: 700; color: #198754; font-size: 2rem; letter-spacing: 0.5px;">Editar Gincana</h2>
    <form id="form-editar-gincana" method="POST" action="{{ route('gincana.update', $gincana) }}">
        @csrf
        @method('PUT')
        <!-- Nome da Gincana -->
        <div style="margin-bottom: 16px;">
            <label for="nome" style="display: block; font-weight: bold; margin-bottom: 6px;">Nome da Gincana</label>
            <input type="text" id="nome" name="nome" value="{{ $gincana->nome }}" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
        </div>
        <!-- Duração -->
        <div style="margin-bottom: 16px;">
            <label for="duracao" style="display: block; font-weight: bold; margin-bottom: 6px;">Duração</label>
            <select id="duracao" name="duracao" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                <option value="24" {{ $gincana->duracao == 24 ? 'selected' : '' }}>24 horas</option>
                <option value="48" {{ $gincana->duracao == 48 ? 'selected' : '' }}>48 horas</option>
                <option value="72" {{ $gincana->duracao == 72 ? 'selected' : '' }}>72 horas</option>
            </select>
        </div>
        <!-- Mapa do Google Maps -->
        <div style="margin-bottom: 16px;">
            <label style="display: block; font-weight: bold; margin-bottom: 6px;">Escolha o local do personagem perdido</label>
            <div id="map" style="height: 300px; width: 100%; border: 1px solid #ccc; border-radius: 4px;"></div>
            <input type="hidden" id="latitude" name="latitude" value="{{ $gincana->latitude }}">
            <input type="hidden" id="longitude" name="longitude" value="{{ $gincana->longitude }}">
        </div>
        <!-- Campo de Cidade -->
        <div style="margin-bottom: 16px;">
            <label for="cidade" style="display: block; font-weight: bold; margin-bottom: 6px;">Cidade / Localização</label>
            <div style="display: flex; gap: 8px;">
                <input type="text" id="cidade" placeholder="Digite a cidade ou endereço" style="flex: 1; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                <button type="button" onclick="buscarCidade()" style="padding: 8px 12px; background-color: #0d6efd; color: white; border: none; border-radius: 4px; cursor: pointer;">
                    Buscar
                </button>
            </div>
        </div>
        <!-- Texto de Contextualização -->
        <div style="margin-bottom: 16px;">
            <label for="contexto" style="display: block; font-weight: bold; margin-bottom: 6px;">Texto de Contextualização (fala do personagem)</label>
            <textarea id="contexto" name="contexto" rows="3" maxlength="255" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">{{ $gincana->contexto }}</textarea>
        </div>
        <!-- Privacidade -->
        <div style="margin-bottom: 16px;">
            <label style="display: block; font-weight: bold; margin-bottom: 6px;">Privacidade</label>
            <div style="margin-bottom: 8px;">
                <input type="radio" name="privacidade" id="publica" value="publica" {{ $gincana->privacidade == 'publica' ? 'checked' : '' }}>
                <label for="publica">Pública (qualquer um pode jogar)</label>
            </div>
            <div>
                <input type="radio" name="privacidade" id="privada" value="privada" {{ $gincana->privacidade == 'privada' ? 'checked' : '' }}>
                <label for="privada">Privada (apenas quem tem o link pode jogar)</label>
            </div>
        </div>
        <!-- Botões -->
        <div style="display: flex; flex-wrap: wrap; gap: 12px; justify-content: center; margin-top: 18px;">
            <button type="submit" style="padding: 10px 28px; background-color: #198754; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; font-size: 1.08em;">
                Salvar Alterações
            </button>
            <a href="{{ route('gincana.index') }}" style="padding: 10px 28px; background-color: #6c757d; color: white; text-decoration: none; border-radius: 4px; display: inline-block; font-weight: 600; font-size: 1.08em;">
                Cancelar
            </a>
        </div>
    </form>
    <!-- <form method="POST" action="{{ route('gincana.destroy', $gincana) }}" style="margin-top: 24px; text-align: center;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger" style="padding: 10px 28px; background-color: #dc3545; color: white; border: none; border-radius: 4px; font-weight: 600; font-size: 1.08em;" onclick="return confirm('Tem certeza que deseja excluir esta gincana?')">Excluir Gincana</button>
    </form> -->
</div>
@endsection
<script>
    let map, marker, geocoder;
    function initMap() {
        const defaultLocation = { lat: parseFloat('{{ $gincana->latitude }}'), lng: parseFloat('{{ $gincana->longitude }}') };
        map = new google.maps.Map(document.getElementById('map'), {
            center: defaultLocation,
            zoom: 12
        });
        geocoder = new google.maps.Geocoder();
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
        });
        map.addListener('click', function(event) {
            marker.setPosition(event.latLng);
            updateLatLngFields(event.latLng);
        });
    }
    function buscarCidade() {
        const endereco = document.getElementById('cidade').value;
        if (!endereco) return;
        geocoder.geocode({ address: endereco }, function (results, status) {
            if (status === 'OK') {
                const location = results[0].geometry.location;
                map.setCenter(location);
                map.setZoom(14);
                marker.setPosition(location);
                document.getElementById('latitude').value = location.lat();
                document.getElementById('longitude').value = location.lng();
            } else {
                alert('Local não encontrado: ' + status);
            }
        });
    }
    window.onload = initMap;
</script>
