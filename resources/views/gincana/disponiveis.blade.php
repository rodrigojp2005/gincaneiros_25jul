@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">🌟 Gincanas Disponíveis</h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Veja todas as gincanas públicas disponíveis para jogar!
            </p>
        </div>

        @if($gincanasDisponiveis->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($gincanasDisponiveis as $gincana)
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border-l-4 border-blue-500">
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-xl font-bold text-gray-900 line-clamp-2">
                                    {{ $gincana->nome }}
                                </h3>
                                <span class="px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    🆕 Disponível
                                </span>
                            </div>
                            <div class="flex items-center gap-2 mb-3">
                                <div class="w-8 h-8 bg-blue-300 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-medium text-blue-800">
                                        {{ $gincana->user ? substr($gincana->user->name, 0, 1) : '?' }}
                                    </span>
                                </div>
                                <span class="text-sm text-gray-600">
                                    Criada por {{ $gincana->user ? $gincana->user->name : 'Usuário Desconhecido' }}
                                </span>
                            </div>
                            <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                                {{ $gincana->contexto }}
                            </p>
                            <div class="space-y-2 mb-4">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Duração:</span>
                                    <span class="text-sm font-medium text-gray-700">
                                        {{ $gincana->duracao }} {{ $gincana->duracao == 1 ? 'local' : 'locais' }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Privacidade:</span>
                                    <span class="text-sm font-medium text-green-600">
                                        Pública
                                    </span>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <a href="{{ route('gincana.show', $gincana) }}" 
                                   class="flex-1 bg-green-600 text-white text-center px-4 py-2 rounded-lg hover:bg-green-700 transition-colors duration-200 text-sm font-medium">
                                    🎮 Jogar Agora
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-16">
                <div class="max-w-md mx-auto">
                    <div class="text-6xl mb-6">😕</div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">
                        Nenhuma gincana disponível no momento
                    </h3>
                    <p class="text-gray-600 mb-8">
                        Aguarde novas gincanas ou crie uma!
                    </p>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endsection
