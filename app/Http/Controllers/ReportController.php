<?php

namespace App\Http\Controllers;

use App\Actions\Report\GetReport;
use App\Models\Customer;
use App\Models\VencimentoSummary;
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
        $relatorio = $request->route('relatorio');
        $tipo = $request->route('tipo');

        if ($relatorio === 'receitas') {
            switch ($tipo) {
                case 'vencimento':
                    $years = VencimentoSummary::query()
                        ->selectRaw('YEAR(data_vencimento_original) as years')
                        ->groupByRaw('YEAR(data_vencimento_original)')
                        ->pluck('years');
                    break;
            }
        }

        return Inertia::render('Reports/Report', [
            'customer' => $customer,
            'relatorio' => $relatorio,
            'tipo' => $tipo,
            'license' => env('FLEXMONSTER_LICENSE'),
            'years' => $years->toArray()
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
        $year = $request->route('year') ?? date('Y');
        return (new GetReport())->execute($customer, [
            'relatorio' => $request->route('relatorio'),
            'tipo' => $request->route('tipo'),
            'year' => $year,
            'anoConta' => $request->input('Ano'),
            'month' => $request->input('Mes'),
            'accounts' => $request->input('contas')
        ]);
    }
}
