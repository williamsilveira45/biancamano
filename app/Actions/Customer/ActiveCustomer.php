<?php

namespace App\Actions\Customer;

use App\Helpers\TextFormatting;
use App\Models\Customer;
use App\Http\Traits\Actions\ModelActionBase;
use App\Http\Traits\Actions\ResponseMessage;
use Exception;
use Illuminate\Support\Facades\Validator;

/**
 * @method bool execute(Customer $customer, array $data = [])
 * @property Customer $actionRecord
 */
class ActiveCustomer
{
    use ModelActionBase;
    use ResponseMessage;

    /**
     * @return bool|array
     * @throws Exception
     */
    protected function main()
    {
        try {
            $this->validateInputs();
            $this->update();
            $text = $this->request->input('active') ? 'Ativado com sucesso' : 'Desativado com sucesso';

            return $this->responseSuccess($text);
        } catch (Exception $e) {
            return $this->responseFailure($e->getMessage());
        }
    }

    /**
     * return void
     */
    private function update()
    {
        $this->actionRecord->active = $this->request->input('active');
        $this->actionRecord->save();
    }

    /**
     * @throws Exception
     */
    private function validateInputs()
    {
        if (empty($this->actionRecord)) {
            throw new Exception('Cliente não encontrado');
        }

        $validator = Validator::make(
            $this->request->all(),
            [
                'active' => 'required|boolean',
            ]
        );

        if ($validator->fails()) {
            throw new Exception(
                TextFormatting::getValidatorString($validator)
            );
        }
    }
}
