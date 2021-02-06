<?php

namespace App\Actions\Report;

use App\Exceptions\GracefulDetailedException;
use App\Helpers\TextFormatting;
use App\Models\Customer;
use App\Http\Traits\Actions\ModelActionBase;
use App\Http\Traits\Actions\ResponseMessage;
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
            'tipo' => $data['tipo'] ?? 'vencimento',
        ];
    }

    /**
     * @return bool|array
     * @throws Exception
     */
    protected function main()
    {
        try {
            $data = $this->actionRecord->summariesVencimento()->get()->toArray();
            $sctructure = [
                'id' => [
                    'type' => 'id'
                ],
                'emissao_nota' => [
                    'type' => 'string',
                    'caption' => 'Emissão da Nota',
                ],
                'data_vencimento_original' => [
                    'type' => 'date string',
                    'caption' => 'Data Vencimento Org.'
                ],
                'competencia' => [
                    'type' => 'date string',
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
                    'type' => 'date string',
                    'caption' => 'Data Entrada'
                ],
                'emissora_titulo' => [
                    'type' => 'string',
                    'caption' => 'Emissora Título',
                ],
                'titulo_pago' => [
                    'type' => 'string',
                    'caption' => 'Título Pago'
                ]
             ];

            $totalData = json_encode([$data, $sctructure]);

            $totalData = str_replace("[", "", $totalData);
            $totalData = str_replace("]", "", $totalData);
            $totalData = "[" . $totalData . "]";

            return response($totalData)->header('Content-Type', 'application/json');
        } catch (Exception $e) {
            return $this->responseFailure('error', $e->getMessage());
        }
    }
}
