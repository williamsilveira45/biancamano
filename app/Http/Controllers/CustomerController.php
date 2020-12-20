<?php

namespace App\Http\Controllers;

use App\Actions\Customer\CreateCustomer;
use App\Models\Customer;
use App\Http\Responses\Customers\CustomersListResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Laravel\Jetstream\Http\Controllers;

class CustomerController extends Controller
{
    /**
     * @return \Inertia\Response
     */
    public function show()
    {
        return Inertia::render('Customers/Show');
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
        return (new CreateCustomer())->execute($request);
    }
}
