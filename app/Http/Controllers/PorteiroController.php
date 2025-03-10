<?php

namespace App\Http\Controllers;

use App\Models\Porteiro;
use Illuminate\Http\Request;

class PorteiroController extends Controller
{
    // Listar todos os porteiros
    public function index() {
        return response()->json(Porteiro::all(), 200);
    }

    // Cadastrar um novo porteiro
    public function store(Request $request) {
        $request->validate([
            'nome' => 'required|string|max:255',
            'telefone' => 'nullable|string|max:20'
        ]);

        $porteiro = Porteiro::create($request->all());
        return response()->json($porteiro, 201);
    }

    // Exibir um porteiro específico
    public function show($id) {
        $porteiro = Porteiro::find($id);

        if (!$porteiro) {
            return response()->json(['message' => 'Porteiro não encontrado']);
        }

        return response()->json($porteiro, 200);
    }

    // Atualizar um porteiro
    public function update(Request $request, $id) {
        $porteiro = Porteiro::find($id);

        if (!$porteiro) {
            return response()->json(['message' => 'Porteiro não encontrado']);
        }

        $porteiro->update($request->all());
        return response()->json($porteiro, 200);
    }

    // Excluir um porteiro
    public function destroy($id) {
        $porteiro = Porteiro::find($id);

        if (!$porteiro) {
            return response()->json(['message' => 'Porteiro não encontrado!']);
        }

        return response()->json(['message' => 'Porteiro removido com sucesso!']);
    }
}
