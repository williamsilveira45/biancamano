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
 * @method bool execute(CustomerContas $customerContas, array $data = [])
 * @property CustomerContas $actionRecord
 */
class UpdateCustomerConta
{
    use ModelActionBase;
    use ResponseMessage;

    /**
     * @param array $data
     */
    protected function setParameters(array $data): void
    {
        $this->data = [
            'name' => $data['name'] ?? '',
            'conta' => $data['conta'] ?? '',
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

            $this->actionRecord->nome_csv = $this->data['name'];
            $this->actionRecord->nome_sha1 = sha1($this->data['name']);
            $this->actionRecord->conta_id = $this->data['conta'];

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
