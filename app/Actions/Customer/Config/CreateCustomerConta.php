<?php

namespace App\Actions\Customer\Config;

use App\Exceptions\GracefulDetailedException;
use App\Helpers\TextFormatting;
use App\Http\Traits\Actions\ActionBase;
use App\Http\Traits\Actions\ModelActionBase;
use App\Http\Traits\Actions\ResponseMessage;
use App\Models\Customer;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @method bool execute(Customer $customer, array $data = [])
 * @property Customer $actionRecord
 */
class CreateCustomerConta
{
    use ModelActionBase;
    use ResponseMessage;

    protected function setParameters(array $data): void
    {
        $this->data = [
          'name' => $data['name'] ?? '',
          'conta' => $data['conta'] ?? '',
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

            $this->actionRecord->contas()->create([
                'conta_id' => $this->data['conta'],
                'nome_csv' => $this->data['name'],
                'nome_sha1' => sha1($this->data['name'])
            ]);

            return $this->responseSuccess('Cliente cadastrado com sucesso');
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
            'name' => ['required', 'string', 'max:255'],
            'conta' => ['required', 'integer'],
        ]);

        if ($validator->fails()) {
            throw new GracefulDetailedException('Dados inválidos',
                TextFormatting::getErrorsToArray($validator)
            );
        }
    }
}
