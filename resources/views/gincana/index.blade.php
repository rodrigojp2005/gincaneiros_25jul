@extends('layouts.app')
@section('content')
<div class="container" style="max-width: 800px; margin: 10px auto 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px;">
        <h2 style="margin: 0; font-weight: 600; color: #198754; font-size: 2rem;">Gincanas que Criei</h2>
        <!-- não faz sentido esses botoes de ver ranking na view de gincanas que criei, pois se criei, ja sei o ranking, logo nao preciso ver o ranking aqui, e sim na view de ranking -->
        <!-- <div style="display: flex; gap: 10px;">
            <a href="{{ route('ranking.index') }}" 
               style="background: #6f42c1; color: white; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-size: 14px;">
                🏆 Rankings
            </a>
            <a href="{{ route('ranking.geral') }}" 
               style="background: #fd7e14; color: white; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-size: 14px;">
                🌟 Ranking Geral
            </a>
        </div> -->
    </div>
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
                                <button onclick="copyGincanaLink({{ $gincana->id }})" title="Compartilhar via WhatsApp ou copiar link" style="background: none; border: none; color: #25D366; font-size: 1.3em; vertical-align: middle; display: flex; align-items: center; cursor: pointer;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 32 32"><path d="M16 3C9.373 3 4 8.373 4 15c0 2.385.668 4.617 1.934 6.594L4 29l7.594-1.934A12.96 12.96 0 0 0 16 27c6.627 0 12-5.373 12-12S22.627 3 16 3zm0 22c-1.98 0-3.91-.52-5.594-1.508l-.406-.234-4.508 1.148 1.148-4.508-.234-.406A10.96 10.96 0 0 1 6 15c0-5.514 4.486-10 10-10s10 4.486 10 10-4.486 10-10 10zm5.406-7.594c-.297-.148-1.758-.867-2.031-.967-.273-.099-.47-.148-.668.148-.198.297-.767.967-.94 1.166-.173.198-.347.223-.644.074-.297-.148-1.255-.463-2.39-1.477-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.457.13-.605.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.372-.025-.52-.074-.148-.668-1.611-.915-2.205-.242-.583-.487-.504-.668-.513-.173-.009-.372-.011-.57-.011-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.148.198 2.099 3.205 5.086 4.369.712.307 1.267.491 1.701.629.715.228 1.366.196 1.88.119.574-.085 1.758-.719 2.007-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/></svg>
                                </button>

@section('scripts')
<script>
    function copyGincanaLink(gincanaId) {
        const url = `${window.location.origin}/gincana/${gincanaId}`;
        if (navigator.share) {
            navigator.share({
                title: 'Gincana',
                text: 'Venha jogar minha gincana e descubra se você consegue vencer o meu desafio! 🏆🎯',
                url: url
            }).catch(() => {
                // fallback caso o usuário cancele
            });
        } else {
            navigator.clipboard.writeText(url).then(function() {
                alert('Link copiado!');
            }, function() {
                alert('Não foi possível copiar o link.');
            });
        }
    }
</script>
@endsection
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
