<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\PlanoConta;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CustomerConfigController extends Controller
{

    /**
     * @param Customer $customer
     * @return \Inertia\Response
     */
    public function config(Customer $customer)
    {
        $planoContas = PlanoConta::all()->pluck('nome_conta', 'id');

        return Inertia::render('Customers/Config', [
            'customer' => $customer,
            'plano_contas' => $planoContas,
        ]);
    }

    /**
     * @param Request $request
     * @return void
     */
    public function readfile(Request $request)
    {
        $data = $request->input('csv');

        $x = [];
        foreach ($data as $info) {
            if (!in_array($info['Conta'], $x)) {
                $x[] = $info['Conta'];
            }
        }
        return response()->json([$x]);
    }

    /**
     * @param Request $request
     */
    public function regcontas(Request $request) {

        dd($request->all());
    }
}
