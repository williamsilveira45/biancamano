<?php

namespace App\Actions\Customer\Config;

use App\Models\Customer;
use App\Http\Traits\Actions\ModelActionBase;
use App\Http\Traits\Actions\ResponseMessage;
use App\Models\CustomerContas;
use Exception;

/**
 * @method bool execute(CustomerContas $conta, array $data = [])
 * @property CustomerContas $actionRecord
 */
class DeleteConta
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
            return $this->responseSuccess('Conta excluída com sucesso');
        } catch (Exception $e) {
            return $this->responseFailure($e->getMessage());
        }
    }

    protected function setParameters(array $data): void
    {
        // TODO: Implement setParameters() method.
    }
}
