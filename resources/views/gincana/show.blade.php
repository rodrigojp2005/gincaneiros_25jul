@extends('layouts.app')
@section('content')
<div class="container" style="max-width: 600px; margin: 40px auto;">
    <h2 style="margin-bottom: 18px;">Detalhes da Gincana</h2>
    <div class="card" style="padding: 20px;">
        <p><strong>Nome:</strong> {{ $gincana->nome }}</p>
        <p><strong>Duração:</strong> {{ $gincana->duracao }} horas</p>
        <p><strong>Localização:</strong> {{ $gincana->latitude }}, {{ $gincana->longitude }}</p>
        <p><strong>Privacidade:</strong> {{ ucfirst($gincana->privacidade) }}</p>
        <p><strong>Contexto:</strong> {{ $gincana->contexto }}</p>
        <p><strong>Criada em:</strong> {{ $gincana->created_at->format('d/m/Y H:i') }}</p>
    </div>
    <a href="{{ route('gincana.index') }}" class="btn btn-secondary" style="margin-top: 20px;">Voltar para lista</a>
</div>
@endsection
