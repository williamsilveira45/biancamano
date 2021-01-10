<?php

namespace App\Http\Controllers;

use App\Actions\PlanoContas\CreateConta;
use App\Actions\PlanoContas\DeleteConta;
use App\Actions\PlanoContas\UpdateConta;
use App\Http\Responses\PlanoContas\PlanoContasResponse;
use App\Models\PlanoConta;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PlanoContasController extends Controller
{
    /**
     * @return \Inertia\Response
     */
    public function show()
    {
        return Inertia::render('PlanoContas/Show');
    }

    public function json()
    {
        return new PlanoContasResponse();
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function store(Request $request)
    {
        return (new CreateConta())->execute($request->all());
    }

    /**
     * @param PlanoConta $conta
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function update(PlanoConta $conta, Request $request)
    {
        return (new UpdateConta())->execute($conta, $request->all());
    }

    /**
     * @param PlanoConta $conta
     * @param Request $request
     * @return bool
     * @throws \Exception
     */
    public function delete(PlanoConta $conta, Request $request) {
        return (new DeleteConta())->execute($conta, $request->all());
    }
}
