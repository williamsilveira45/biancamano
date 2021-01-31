<?php

namespace App\Http\Controllers;

use App\Actions\Customer\Config\ActiveConta;
use App\Actions\Customer\Config\CreateCustomerConta;
use App\Actions\Customer\Config\DeleteConta;
use App\Actions\Customer\Config\RegisterContas;
use App\Actions\Customer\Config\UpdateCustomerConta;
use App\Http\Responses\Customers\CustomersContasListResponse;
use App\Models\Customer;
use App\Models\CustomerContas;
use Illuminate\Http\Request;

class CustomerContasController extends Controller
{
    public function store(Customer $customer, Request $request)
    {
        return (new CreateCustomerConta())->execute($customer, $request->all());
    }

    /**
     * @param Customer $customer
     * @param CustomerContas $customerContas
     * @param Request $request
     * @return bool
     * @throws \Exception
     */
    public function update(Customer $customer, CustomerContas $customerContas, Request $request)
    {
        return (new UpdateCustomerConta())->execute($customerContas, $request->all());
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
     * @return bool
     * @throws \Exception
     */
    public function regcontas(Request $request, Customer $customer)
    {
        return (new RegisterContas())->execute($customer, $request->all(['contas', 'contas_sistema']));
    }

    /**
     * @return CustomersContasListResponse
     */
    public function jsonContas()
    {
        return new CustomersContasListResponse();
    }

    /**
     * @param Customer $customer
     * @param CustomerContas $customercontas
     * @param Request $request
     * @return bool
     * @throws \Exception
     */
    public function active(Customer $customer, CustomerContas $customercontas, Request $request)
    {
        return (new ActiveConta())->execute($customercontas, $request->all());
    }

    /**
     * @param Customer $customer
     * @param CustomerContas $customercontas
     * @param Request $request
     * @return bool
     * @throws \Exception
     */
    public function delete(Customer $customer, CustomerContas $customercontas, Request $request)
    {
        return (new DeleteConta())->execute($customercontas, $request->all());
    }
}
