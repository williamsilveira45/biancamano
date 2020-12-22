<?php

namespace App\Http\Traits\Actions;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

/**
 * Trait ActionBase
 * @package App\Http\Traits\Actions
 */
trait ActionBase
{
    /** @var Request */
    protected $request;

    /**
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function execute(Request $request)
    {
        $this->request = $request;

        return $this->init();
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    protected function init()
    {
        try {
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
     * @param string $path
     * @param array $params
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirect($path, array $params = [])
    {
        return Redirect::route($path, $params);
    }

    /**
     * Main function - add the main logic for the class here
     */
    abstract protected function main();
}
