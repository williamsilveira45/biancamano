<?php

namespace App\Actions\Report;

use App\Exceptions\GracefulDetailedException;
use App\Helpers\TextFormatting;
use App\Models\Customer;
use App\Http\Traits\Actions\ModelActionBase;
use App\Http\Traits\Actions\ResponseMessage;
use App\Models\PlanoConta;
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
        ];
    }

    /**
     * @return bool|array
     * @throws Exception
     */
    protected function main()
    {
        try {
            $data = $this->getData();
            $sctructure = [
                'conta_id' => [
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
    private function getData()
    {
        $getPlanoContas = PlanoConta::all()->pluck('nome_conta', 'id');
        return $this->actionRecord
            ->summariesVencimento()
            ->ano($this->data['year'])
            ->get()
            ->transform(function ($item) use ($getPlanoContas) {
                $item->conta_sistema = $getPlanoContas[$item->conta_id] ?? 'Não associado';
                return $item;
            })
            ->toArray();
    }
}
