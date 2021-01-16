<?php

namespace App\Http\Controllers;

use App\Actions\Customer\ActiveCustomer;
use App\Actions\Customer\CreateCustomer;
use App\Actions\Customer\DeleteCustomer;
use App\Actions\Customer\UpdateCustomer;
use App\Http\Responses\Customers\CustomersListResponse;
use App\Models\Customer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CustomerController extends Controller
{
    /**
     * @return \Inertia\Response
     */
    public function show()
    {
        return Inertia::render('Customers/Show');
    }

    /**
     * @param Customer $customer
     * @return \Inertia\Response
     */
    public function config(Customer $customer)
    {
        return Inertia::render('Customers/Config', [
            'customer' => $customer,
        ]);
    }

    public function json()
    {
        return new CustomersListResponse();
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function store(Request $request)
    {
        return (new CreateCustomer())->execute($request->all());
    }

    /**
     * @param Customer $customer
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function update(Customer $customer, Request $request)
    {
        return (new UpdateCustomer())->execute($customer, $request->all());
    }

    /**
     * @param Customer $customer
     * @param Request $request
     * @return bool
     * @throws \Exception
     */
    public function delete(Customer $customer, Request $request) {
        return (new DeleteCustomer())->execute($customer, $request->all());
    }

    /**
     * @param Customer $customer
     * @param Request $request
     * @return bool
     * @throws \Exception
     */
    public function active(Customer $customer, Request $request)
    {
        return (new ActiveCustomer())->execute($customer, $request->all());
    }
}
