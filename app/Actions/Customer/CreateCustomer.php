<?php

namespace App\Actions\Customer;

use App\Exceptions\GracefulDetailedException;
use App\Helpers\TextFormatting;
use App\Http\Traits\Actions\ActionBase;
use App\Http\Traits\Actions\ResponseMessage;
use App\Models\Customer;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Class CreateCustomer
 * @package App\Actions\Customer
 * @property Request $request
 */
class CreateCustomer
{
    use ActionBase;
    use ResponseMessage;

    protected function setParameters(array $data): void
    {
        $this->data = [
          'name' => $data['name'] ?? '',
          'cnpj' => $data['cnpj'] ?? '',
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

            Customer::create([
                'name' => strtoupper($a[1]),
                'cnpj' => $this->data['cnpj']
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
            'cnpj' => ['required', 'cnpj', 'unique:customers', 'max:18'],
        ]);

        if ($validator->fails()) {
            throw new GracefulDetailedException('Dados inválidos',
                TextFormatting::getErrorsToArray($validator)
            );
        }
    }
}
