<?php

namespace App\Actions\PlanoContas;

use App\Exceptions\GracefulDetailedException;
use App\Helpers\TextFormatting;
use App\Models\Customer;
use App\Http\Traits\Actions\ModelActionBase;
use App\Http\Traits\Actions\ResponseMessage;
use App\Models\PlanoConta;
use Exception;
use Illuminate\Support\Facades\Validator;

/**
 * @method bool execute(PlanoConta $conta, array $data = [])
 * @property PlanoConta $actionRecord
 */
class UpdateConta
{
    use ModelActionBase;
    use ResponseMessage;

    /**
     * @param array $data
     */
    protected function setParameters(array $data): void
    {
        $this->data = [
            'nome_conta' => $data['nome_conta'] ?? '',
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

            $this->actionRecord->nome_conta = strtoupper($this->data['nome_conta']);

            $this->actionRecord->save();

            return $this->responseSuccess('Conta atualizada com sucesso');
        } catch (GracefulDetailedException $e) {
            return $this->responseFailure('inputError', $e->getMessage(), ['errors' => $e->getDetails()] ?? []);
        } catch (Exception $e) {
            return $this->responseFailure('error', $e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    private function validation()
    {
        $validator = Validator::make($this->data, [
            'nome_conta' => ['required', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            throw new GracefulDetailedException('Dados inválidos',
                TextFormatting::getErrorsToArray($validator)
            );
        }
    }
}
