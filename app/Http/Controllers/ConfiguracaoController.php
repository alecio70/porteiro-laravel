<?php

namespace App\Http\Controllers;

use App\Models\Configuracao;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ConfiguracaoController extends Controller
{
    public function index() {
        return response()->json(Configuracao::all(), 200);
    }

    // Método para salvar ou atualizar a configuração
    public function store(Request $request) {
        $anoAtual = Carbon::now()->year;

        // Validação: ano não pode ser anterior ao ano atual
        if ($request->ano && $request->ano < $anoAtual) {
            return response()->json(['message' => 'Não é permitido cadastrar configurações para anos anteriores'], 400);
        }

        $ano = $request->ano;

        // Validando os dias da semana
        $diasValidos = ['Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado', 'Domingo'];
        $diasSelecionados = $request->dias_semana;

        // Verifica se todos os dias são válidos
        foreach ($diasSelecionados as $dia) {
            if (!in_array($dia, $diasValidos)) {
                return response()->json(['message' => 'dia inválido: ' . $dia], 400);
            }
        }

        // Verifica se já existe uma configuração
        $configuracaoExistente = Configuracao::first();

        // Se existir, atualiza a configuração com o novo ano e os dias da semana
        if ($configuracaoExistente) {
            $configuracaoExistente->update([
                'ano' => $ano,
                'dias_semana' => $diasSelecionados
            ]);
            
            return response()->json(['message' => 'Configuração atualizada com sucesso']);
        } 

        // Se não existir configuração, cria uma nova
        Configuracao::create([
            'ano' => $ano,  // Utiliza o ano fornecido pelo usuário
            'dias_semana' => $diasSelecionados
        ]);

        return response()->json(['message' => 'Configuração criada com sucesso']);
    }

}
