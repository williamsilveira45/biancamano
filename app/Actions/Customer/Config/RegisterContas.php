<?php

namespace App\Actions\Customer\Config;

use App\Exceptions\GracefulDetailedException;
use App\Helpers\TextFormatting;
use App\Models\Customer;
use App\Http\Traits\Actions\ModelActionBase;
use App\Http\Traits\Actions\ResponseMessage;
use App\Models\CustomerContas;
use Exception;
use Illuminate\Support\Facades\Validator;

/**
 * @method bool execute(Customer $customer, array $data = [])
 * @property Customer $actionRecord
 */
class RegisterContas
{
    use ModelActionBase;
    use ResponseMessage;

    /**
     * @param array $data
     */
    protected function setParameters(array $data): void
    {
        $this->data = [
            'contas' => $data['contas'] ?? [],
            'contas_sistema' => $data['contas_sistema'] ?? [],
        ];
    }

    /**
     * @return bool|array
     * @throws Exception
     */
    protected function main()
    {
        try {
            $this->validation();

            $this->insertContas();

            return $this->responseSuccess('Plano de contas configuradas neste cliente.');
        } catch (GracefulDetailedException $e) {
            return $this->responseFailure('inputError', $e->getMessage(), ['errors' => $e->getDetails()] ?? []);
        } catch (Exception $e) {
            return $this->responseFailure('error', $e->getMessage());
        }
    }

    private function insertContas()
    {
        foreach ($this->data['contas_sistema'] as $id => $contaSistema) {
            if (empty($contaSistema)) {
                continue;
            }

            $this->actionRecord->contas()->create([
                'conta_id' => $contaSistema,
                'nome_csv' => $this->data['contas'][$id],
                'nome_sha1' => sha1($this->data['contas'][$id])
            ]);
        }
    }

    /**
     * @throws Exception
     */
    private function validation()
    {
        $validator = Validator::make($this->data, [
            'contas' => ['required', 'array'],
            'contas_sistema' => ['required', 'array'],
        ]);

        if ($validator->fails()) {
            throw new GracefulDetailedException('Dados inválidos',
                TextFormatting::getErrorsToArray($validator)
            );
        }
    }
}
