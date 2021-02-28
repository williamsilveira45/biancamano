<?php

namespace App\Http\Controllers;

use App\Actions\Report\GetReport;
use App\Models\Customer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReportController extends Controller
{
    /**
     * @return \Inertia\Response
     */
    public function show()
    {
        $customers = Customer::pluck('name', 'id');
        return Inertia::render('Reports/Show', ['customers' => $customers]);
    }

    /**
     * @param Customer $customer
     * @return \Inertia\Response
     */
    public function reportType(Customer $customer) {
        return Inertia::render('Reports/Reports', ['customer' => $customer]);
    }

    /**
     * @param Customer $customer
     * @param Request $request
     * @return \Inertia\Response
     */
    public function report(Customer $customer, Request $request) {
        return Inertia::render('Reports/Report', [
            'customer' => $customer,
            'tipo' => $request->route('tipo'),
        ]);
    }

    /**
     * @param Request $request
     * @param Customer $customer
     * @return bool
     * @throws \Exception
     */
    public function json(Request $request, Customer $customer)
    {
        return (new GetReport())->execute($customer);
    }
}
