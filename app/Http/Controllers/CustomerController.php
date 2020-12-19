<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Http\Responses\Customers\CustomersListResponse;
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

    public function json()
    {
        return new CustomersListResponse();
    }

    public function create()
    {
        dd('ADD');
    }
}
