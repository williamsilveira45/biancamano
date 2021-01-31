<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\PlanoConta;
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
}
