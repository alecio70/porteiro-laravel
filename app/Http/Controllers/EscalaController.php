<?php

namespace App\Http\Controllers;

use App\Models\Configuracao;
use App\Models\Escala;
use App\Models\Porteiro;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EscalaController extends Controller
{
    public function gerarEscala() {
        $configuracao = Configuracao::first();
        if (!$configuracao) {
            return response()->json(['error' => 'Nenhuma configuração encontrado', 400]);
        }

        $diasSelecionado = $configuracao->dias_semana;
        $porteiros = Porteiro::all();
        if ($porteiros->count() < 2) {
            return response()->json(['error' => 'É necessário pelo menos 2 porteiros'], 400);
        }

        $anoAtual = date('Y');
        $dataInicio = Carbon::create($anoAtual, 1, 1);
        $dataFim = Carbon::create($anoAtual, 12, 31);

        $escala = [];
        $ultimoPorteiro1 = null;
        $ultimoPorteiro2 = null;

        while ($dataInicio->lte($dataFim)) {
            if (in_array($dataInicio->format('l'), $diasSelecionado)) {
                $disponiveis = $porteiros->pluck('id')->toArray();
                shuffle($disponiveis);

                do {
                    $porteiro1 = array_shift($disponiveis);
                } while ($porteiro1 == $ultimoPorteiro1);

                do {
                    $porteiro2 = array_shift($disponiveis);
                } while ($porteiro2 == $ultimoPorteiro2 || $porteiro2 == $porteiro1);

                Escala::updateOrCreate(
                    ['data' => $dataInicio->toDateString()],
                    ['porteiro1_id' => $porteiro1, 'porteiro2_id' => $porteiro2]
                );

                $ultimoPorteiro1 = $porteiro1;
                $ultimoPorteiro2 = $porteiro2;
            }

            $dataInicio->addDay();
        }
        
        return response()->json(['message' => 'Escala gerada com sucesso']);
    }
}
