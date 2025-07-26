@extends('layouts.app')
@section('content')
<div class="container" style="max-width: 800px; margin: 10px auto 0 auto;">
    <h2 style="margin-bottom: 18px; font-weight: 600; color: #198754; font-size: 2rem;">Lista de Gincanas Criadas</h2>
    @if($gincanas->isEmpty())
        <p style="margin-top: 24px;">Nenhuma gincana cadastrada.</p>
    @else
        <div style="overflow-x:auto; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
        <table class="table" style="width: 100%; border-collapse: separate; border-spacing: 0;">
            <thead>
                <tr style="background: #198754; color: #fff;">
                    <th style="padding: 12px 8px; font-weight: 500;">Nome</th>
                    <th style="padding: 12px 8px; font-weight: 500;">Duração</th>
                    <th style="padding: 12px 8px; font-weight: 500;">Localização</th>
                    <th style="padding: 12px 8px; font-weight: 500;">Privacidade</th>
                    <th style="padding: 12px 8px; font-weight: 500;">Criada em</th>
                    <th style="padding: 12px 8px; font-weight: 500; text-align:center;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($gincanas as $i => $gincana)
                    <tr style="background: {{ $i % 2 == 0 ? '#eafaf1' : '#c7eed8' }};">
                        <td style="padding: 10px 8px;">{{ $gincana->nome }}</td>
                        <td style="padding: 10px 8px;">{{ $gincana->duracao }} horas</td>
                        <td style="padding: 10px 8px;">{{ $gincana->latitude }}, {{ $gincana->longitude }}</td>
                        <td style="padding: 10px 8px;">{{ ucfirst($gincana->privacidade) }}</td>
                        <td style="padding: 10px 8px;">{{ $gincana->created_at->format('d/m/Y H:i') }}</td>
                        <td style="padding: 10px 8px; text-align:center;">
                            <div style="display: flex; justify-content: center; align-items: center; gap: 12px;">
                                <a href="{{ route('gincana.show', $gincana) }}" title="Ver detalhes" style="background: none; border: none; color: #198754; font-size: 1.3em; vertical-align: middle; display: flex; align-items: center;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 16 16"><path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zm-8 4a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-1.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z"/></svg>
                                </a>
                                <a href="{{ route('gincana.edit', $gincana) }}" title="Editar" style="background: none; border: none; color: #ffc107; font-size: 1.3em; vertical-align: middle; display: flex; align-items: center;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 16 16"><path d="M15.502 1.94a.5.5 0 0 1 0 .706l-1.292 1.292-2.121-2.121 1.292-1.292a.5.5 0 0 1 .706 0l1.415 1.415zm-1.75 2.456-2.121-2.121-8.486 8.486a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l8.486-8.486z"/></svg>
                                </a>
                                <form action="{{ route('gincana.destroy', $gincana) }}" method="POST" style="display:inline; margin:0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Excluir" style="background: none; border: none; color: #dc3545; font-size: 1.3em; vertical-align: middle; display: flex; align-items: center;" onclick="return confirm('Tem certeza que deseja excluir esta gincana?')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 16 16"><path d="M5.5 5.5A.5.5 0 0 1 6 5h4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-.5.5H6a.5.5 0 0 1-.5-.5v-7zm-1-1A.5.5 0 0 1 5 4h6a.5.5 0 0 1 .5.5v8A1.5 1.5 0 0 1 10 14H6a1.5 1.5 0 0 1-1.5-1.5v-8A.5.5 0 0 1 5 4zm7-1a.5.5 0 0 1 .5.5V5h-9V3.5A.5.5 0 0 1 5 3h6z"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    @endif
</div>
    <div style="width: 100%; display: flex; justify-content: center; margin-top: 32px;">
        <a href="{{ route('gincana.create') }}" class="btn btn-primary" style="font-weight: 500; background: #198754; border: none; min-width: 220px;">+ Criar Nova Gincana</a>
    </div>
</div>
@endsection
