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
        return Inertia::render('Reports/Show');
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
