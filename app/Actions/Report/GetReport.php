<?php

namespace App\Actions\Report;

use App\Exceptions\GracefulDetailedException;
use App\Helpers\TextFormatting;
use App\Models\Customer;
use App\Http\Traits\Actions\ModelActionBase;
use App\Http\Traits\Actions\ResponseMessage;
use App\Models\PlanoConta;
use App\Models\VencimentoSummary;
use Carbon\Carbon;
use Exception;
use http\Header;
use Illuminate\Support\Facades\Validator;

/**
 * @method bool execute(Customer $customer, array $data = [])
 * @property Customer $actionRecord
 */
class GetReport
{
    use ModelActionBase;
    use ResponseMessage;

    /**
     * @param array $data
     */
    protected function setParameters(array $data): void
    {
        $this->data = [
            'relatorio' => $data['relatorio'] ?? 'despesas',
            'tipo' => $data['tipo'] ?? 'vencimento',
            'year' => $data['year'] ?? date('Y'),
            'anoConta' => $data['anoConta'] ?? date('Y'),
            'month' => $data['month'] ?? '',
            'accounts' => explode(",", $data['accounts']) ?? []
        ];
    }

    /**
     * @return bool|array
     * @throws Exception
     */
    protected function main()
    {
        try {
            $data = empty($this->data['month']) ? $this->getSumData() : $this->getAllData();
            $sctructure = $this->getEstrutura();

            $totalData = json_encode([$sctructure, $data]);

            $totalData = str_replace("[", "", $totalData);
            $totalData = str_replace("]", "", $totalData);
            $totalData = "[" . $totalData . "]";

            return response($totalData)->header('Content-Type', 'application/json');
        } catch (Exception $e) {
            return $this->responseFailure('error', $e->getMessage());
        }
    }

    /**
     * @return array
     */
    private function getSumData()
    {
        $getPlanoContas = PlanoConta::all()->pluck('nome_conta', 'id');
        return $this->actionRecord
            ->summariesVencimento()
            ->ano($this->data['year'])
            ->get()
            ->transform(function ($item) use ($getPlanoContas) {
                $item->cellData = [
                    'ano' => $item->data_vencimento_original->year,
                    'mes' => $item->data_vencimento_original->month,
                    'idConta' => $item->conta_id,
                ];
                $item->conta_sistema = $getPlanoContas[$item->conta_id] ?? 'Não associado';
                return $item;
            })
            ->toArray();
    }

    /**
     * @return array
     */
    private function getAllData()
    {
        $getPlanoContas = PlanoConta::all()->pluck('nome_conta', 'id');

        return $this->actionRecord
            ->trialData()
            ->ano($this->data['anoConta'])
            ->month($this->data['month'])
            ->contasFilter($this->data['accounts'])
            ->get()
            ->transform(function ($item) use ($getPlanoContas) {
                $item->conta_sistema = $getPlanoContas[$item->conta_id] ?? 'Não associado';
                return $item;
            })
            ->toArray();
    }

    private function getEstrutura() {
        if (empty($this->data['month'])) {
            return [
                'cellData' => [
                    'type' => 'id'
                ],
                'conta_sistema' => [
                    'type' => 'string',
                    'caption' => 'Conta Sistema',
                ],
                'emissao_nota' => [
                    'type' => 'string',
                    'caption' => 'Emissão da Nota',
                ],
                'data_vencimento_original' => [
                    'type' => 'date',
                    'caption' => 'Data Vencimento Org.'
                ],
                'competencia' => [
                    'type' => 'date',
                    'caption' => 'Data Competência'
                ],
                'natureza_financeira' => [
                    'type' => 'string',
                    'caption' => 'Natureza Financeira'
                ],
                'operacao' => [
                    'type' => 'string',
                    'caption' => 'Operação'
                ],
                'valor' => [
                    'type' => 'number',
                    'caption' => 'Valor'
                ],
                'data_entrada' => [
                    'type' => 'date',
                    'caption' => 'Data Entrada'
                ],
                'emissora_titulo' => [
                    'type' => 'string',
                    'caption' => 'Emissora Título',
                ],
                'titulo_pago' => [
                    'type' => 'string',
                    'caption' => 'Título Pago'
                ],
            ];
        }

        return [
            'conta_sistema' => [
                'type' => 'string',
                'caption' => 'Conta Sistema',
            ],
            'emissao_nota' => [
                'type' => 'string',
                'caption' => 'Emissão da Nota',
            ],
            'data_vencimento_original' => [
                'type' => 'date',
                'caption' => 'Data Vencimento Org.'
            ],
            'competencia' => [
                'type' => 'date',
                'caption' => 'Data Competência'
            ],
            'natureza_financeira' => [
                'type' => 'string',
                'caption' => 'Natureza Financeira'
            ],
            'operacao' => [
                'type' => 'string',
                'caption' => 'Operação'
            ],
            'valor_original' => [
                'type' => 'number',
                'caption' => 'Valor Original'
            ],
            'valor_recebido' => [
                'type' => 'number',
                'caption' => 'Valor Recebido'
            ],
            'data_entrada' => [
                'type' => 'date',
                'caption' => 'Data Entrada'
            ],
            'emissora_titulo' => [
                'type' => 'string',
                'caption' => 'Emissora Título',
            ],
            'titulo_pago' => [
                'type' => 'string',
                'caption' => 'Título Pago'
            ],
        ];
    }
}
