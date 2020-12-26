<?php

namespace App\Actions\Customer;

use App\Models\Customer;
use App\Http\Traits\Actions\ModelActionBase;
use App\Http\Traits\Actions\ResponseMessage;
use Exception;

/**
 * @method bool execute(Customer $customer, array $data = [])
 * @property Customer $actionRecord
 */
class DeleteCustomer
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
            $this->actionRecord->delete();
            return $this->responseSuccess('Cliente removido com sucesso');
        } catch (Exception $e) {
            return $this->responseFailure($e->getMessage());
        }
    }

    protected function setParameters(array $data): void
    {
        // TODO: Implement setParameters() method.
    }
}
