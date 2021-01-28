<?php

namespace App\Http\Controllers;

use App\Actions\Customer\Config\RegisterContas;
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
        $customer = $customer->with('contas')->first();
        $planoContas = PlanoConta::all()->pluck('nome_conta', 'id');

        return Inertia::render('Customers/Config', [
            'customer' => $customer,
            'plano_contas' => $planoContas,
        ]);
    }

    /**
     * @param Request $request
     * @param Customer $customer
     * @return void
     */
    public function readfile(Request $request, Customer $customer)
    {
        $contasEmUso = $customer->contas()->pluck('nome_sha1')->toArray();

        $data = $request->input('csv');

        $x = [];
        foreach ($data as $info) {
            if (!in_array($info['Conta'], $x) && !in_array(sha1($info['Conta']), $contasEmUso)) {
                $x[] = $info['Conta'];
            }
        }
        return response()->json([$x]);
    }

    /**
     * @param Request $request
     * @param Customer $customer
     */
    public function regcontas(Request $request, Customer $customer)
    {
        return (new RegisterContas())->execute($customer, $request->all(['contas', 'contas_sistema']));
    }
}
