<?php

namespace App\Http\Controllers;

use App\Actions\Customer\Upload;
use App\Models\Customer;
use App\Http\Responses\Files\FilesListResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FileController extends Controller
{
    /**
     * @return \Inertia\Response
     */
    public function show()
    {
        $customers = \App\Models\Customer::active()->pluck('name', 'id');
        return Inertia::render('Files/Show', ['customers' => $customers, 'csrf' => csrf_token()]);
    }

    public function json()
    {
        return new FilesListResponse();
    }

    public function upload(Request $request)
    {
        $customer = Customer::find($request->input('customer_id'));
        return (new Upload())->execute($customer, [
            'file' => $request->file('file')
        ]);
    }
}


