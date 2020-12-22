<?php

namespace App\Http\Traits\Actions;

use Illuminate\Http\Request;

/**
 * Trait ModelActionBase
 * @package App\Http\Traits\Actions
 */
trait ModelActionBase
{
    protected $actionRecord;

    /** @var Request */
    protected $request;

    /**
     * @param \Eloquent|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Builder $actionRecord
     * @param array $data
     * @return mixed
     * @throws \Exception
     */
    public function execute($actionRecord, Request $request)
    {
        // Set global model
        $this->actionRecord = $actionRecord;
        $this->request = $request;
        return $this->init();
    }

    /**
     * @param array $data
     * @return mixed
     * @throws \Exception
     */
    protected function init()
    {
        try {
            // Set parameters
            // Set parameters
            if (!$this->request instanceof Request) {
                throw new \Exception('Não foi identificado nenhuma requisição');
            }

            // Execute main functions
            return $this->main();
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Main function - add the main logic for the class here
     */
    abstract protected function main();
}
