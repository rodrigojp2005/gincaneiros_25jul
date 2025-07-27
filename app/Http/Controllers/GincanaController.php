<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gincana;
use Illuminate\Support\Facades\Auth;

class GincanaController extends Controller
{
    // Salva uma nova gincana
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'duracao' => 'required|integer',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'contexto' => 'required|string|max:255',
            'privacidade' => 'required|in:publica,privada',
        ]);

        $validated['user_id'] = Auth::id();
        $gincana = Gincana::create($validated);

        return redirect()->route('gincana.index')->with('success', 'Gincana criada com sucesso!');
    }
    // Exibe o formulário de criação
    public function create()
    {
        return view('gincana.criar');
    }


    // Exibe formulário de edição
    public function edit(Gincana $gincana)
    {
        return view('gincana.edit', compact('gincana'));
    }

    // Atualiza gincana
    public function update(Request $request, Gincana $gincana)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'duracao' => 'required|integer',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'contexto' => 'required|string|max:255',
            'privacidade' => 'required|in:publica,privada',
        ]);
        $gincana->update($validated);
        return redirect()->route('gincana.index')->with('success', 'Gincana atualizada com sucesso!');
    }

    // Exclui gincana
    public function destroy(Gincana $gincana)
    {
        $gincana->delete();
        return redirect()->route('gincana.index')->with('success', 'Gincana excluída com sucesso!');
    }

    // Exemplo de listagem (pode ser ajustado depois)
    public function index()
    {
        $gincanas = Gincana::where('user_id', Auth::id())->get();
        return view('gincana.index', compact('gincanas'));
    }

    // Exibe detalhes de uma gincana
    public function show(Gincana $gincana)
    {
        $locais = $gincana->locais()->get(['latitude', 'longitude']);
        return view('gincana.show', compact('gincana', 'locais'));
    }

    // Lista as gincanas que o usuário jogou (participou)
    public function jogadas()
    {
        $gincanasJogadas = Auth::user()->gincanasParticipando()
            ->with(['user', 'participacoes' => function($query) {
                $query->where('user_id', Auth::id());
            }])
            ->get();
        
        return view('gincana.jogadas', compact('gincanasJogadas'));
    }
}
