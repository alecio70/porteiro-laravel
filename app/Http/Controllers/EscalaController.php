<?php

namespace App\Http\Controllers;

use App\Models\Configuracao;
use App\Models\Escala;
use App\Models\Porteiro;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EscalaController extends Controller
{
    public function listarEscala() {
        $escala = Escala::with(['porteiro1', 'porteiro2'])->get()->map(function ($item) {
            return [
                'data' => $item->data,
                'porteiro1' => $item->porteiro1 ? $item->porteiro1->nome : null,
                'porteiro2' => $item->porteiro2 ? $item->porteiro2->nome : null,
            ];
        });

        return response()->json($escala);
    }

    public function gerarEscala()
{
    $configuracao = Configuracao::first();
    if (!$configuracao) {
        return response()->json(['error' => 'Nenhuma configuração encontrada'], 400);
    }

    $diasSelecionados = $configuracao->dias_semana;
    if (empty($diasSelecionados)) {
        return response()->json(['error' => 'Nenhum dia da semana configurado'], 400);
    }

    $porteiros = Porteiro::all();
    if ($porteiros->count() < 3) {
        return response()->json(['error' => 'É necessário pelo menos 3 porteiros'], 400);
    }

    $anoAtual = date('Y');
    $dataInicio = Carbon::now();
    $dataFim = Carbon::create($anoAtual, 12, 31);

    $escalaPorteiros = $porteiros->pluck('id')->toArray(); // IDs dos porteiros
    $index = 0; // Índice para alternar os porteiros
    $porteiro1Domingos = [];

    while ($dataInicio->lte($dataFim)) {
        $diaSemana = $this->traduzirDiaSemana($dataInicio->format('l'));

        if (in_array($diaSemana, $diasSelecionados)) {
            // Definir porteiro1 e porteiro2 seguindo a rotação
            $porteiro1 = $escalaPorteiros[$index % count($escalaPorteiros)];
            $porteiro2 = $escalaPorteiros[($index + 1) % count($escalaPorteiros)];
            
            // Se for domingo, garantir que o porteiro1 não repetiu no domingo anterior
            if ($diaSemana === 'Domingo' && in_array($porteiro1, $porteiro1Domingos)) {
                $index++; // Avança para evitar repetição
                $porteiro1 = $escalaPorteiros[$index % count($escalaPorteiros)];
                $porteiro2 = $escalaPorteiros[($index + 1) % count($escalaPorteiros)];
            }

            try {
                Escala::updateOrCreate(
                    ['data' => $dataInicio->toDateString()],
                    [
                        'porteiro1_id' => $porteiro1,
                        'porteiro2_id' => $porteiro2
                    ]
                );

                Log::info("Escala criada para {$dataInicio->toDateString()} - Porteiros: {$porteiro1} e {$porteiro2}");

                // Se for domingo, atualizar a lista de domingos
                if ($diaSemana === 'Domingo') {
                    array_push($porteiro1Domingos, $porteiro1);
                    if (count($porteiro1Domingos) > 1) {
                        array_shift($porteiro1Domingos);
                    }
                }

                // Avançar no índice para rotação
                $index++;

            } catch (\Exception $e) {
                Log::error("Erro ao criar escala: " . $e->getMessage());
                return response()->json(['error' => 'Erro ao salvar escala no banco', 'details' => $e->getMessage()], 500);
            }
        }

        $dataInicio->addDay();
    }

    return response()->json(['message' => 'Escala gerada com sucesso']);
}



    private function traduzirDiaSemana($diaIngles)
    {
        $dias = [
            'Sunday' => 'Domingo',
            'Monday' => 'Segunda-feira',
            'Tuesday' => 'Terça-feira',
            'Wednesday' => 'Quarta-feira',
            'Thursday' => 'Quinta-feira',
            'Friday' => 'Sexta-feira',
            'Saturday' => 'Sábado',
        ];

        return $dias[$diaIngles] ?? $diaIngles;
    }
}
