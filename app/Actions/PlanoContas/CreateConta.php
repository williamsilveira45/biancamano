<?php

namespace App\Actions\PlanoContas;

use App\Exceptions\GracefulDetailedException;
use App\Helpers\TextFormatting;
use App\Http\Traits\Actions\ActionBase;
use App\Http\Traits\Actions\ResponseMessage;
use App\Models\Customer;
use App\Models\PlanoConta;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Class CreateCustomer
 * @package App\Actions\Customer
 * @property Request $request
 */
class CreateConta
{
    use ActionBase;
    use ResponseMessage;

    protected function setParameters(array $data): void
    {
        $this->data = [
          'nome_conta' => $data['nome_conta'] ?? '',
        ];
    }

    /**
     * @return array
     * @throws Exception
     */
    protected function main()
    {
        try {
            $this->validation();

            PlanoConta::create([
                'nome_conta' => strtoupper($this->data['nome_conta']),
            ]);

            return $this->responseSuccess('Conta cadastrada sucesso');
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
