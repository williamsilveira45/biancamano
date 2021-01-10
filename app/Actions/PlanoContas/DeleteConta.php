<?php

namespace App\Actions\PlanoContas;

use App\Models\Customer;
use App\Http\Traits\Actions\ModelActionBase;
use App\Http\Traits\Actions\ResponseMessage;
use App\Models\PlanoConta;
use Exception;

/**
 * @method bool execute(PlanoConta $conta, array $data = [])
 * @property PlanoConta $actionRecord
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
            return $this->responseSuccess('Conta removida com sucesso');
        } catch (Exception $e) {
            return $this->responseFailure($e->getMessage());
        }
    }

    protected function setParameters(array $data): void
    {
        // TODO: Implement setParameters() method.
    }
}
