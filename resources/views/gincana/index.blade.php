@extends('layouts.app')
@section('content')
<div class="container" style="max-width: 800px; margin: 40px auto;">
    <h2 style="margin-bottom: 24px;">Lista de Gincanas</h2>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($gincanas->isEmpty())
        <p>Nenhuma gincana cadastrada.</p>
    @else
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Duração</th>
                    <th>Localização</th>
                    <th>Privacidade</th>
                    <th>Criada em</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($gincanas as $gincana)
                    <tr>
                        <td>{{ $gincana->nome }}</td>
                        <td>{{ $gincana->duracao }} horas</td>
                        <td>{{ $gincana->latitude }}, {{ $gincana->longitude }}</td>
                        <td>{{ ucfirst($gincana->privacidade) }}</td>
                        <td>{{ $gincana->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <a href="{{ route('gincana.show', $gincana) }}" class="btn btn-info btn-sm">Ver detalhes</a>
                            <a href="{{ route('gincana.edit', $gincana) }}" class="btn btn-warning btn-sm" style="margin-left: 5px;">Editar</a>
                            <form action="{{ route('gincana.destroy', $gincana) }}" method="POST" style="display:inline; margin-left: 5px;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir esta gincana?')">Excluir</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
    <a href="{{ route('gincana.create') }}" class="btn btn-primary" style="margin-top: 20px;">Criar Nova Gincana</a>
</div>
@endsection
