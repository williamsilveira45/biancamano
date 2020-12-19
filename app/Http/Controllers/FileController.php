<?php

namespace App\Http\Controllers;

use App\Actions\Customer\Upload;
use App\Models\Customer;
use App\Models\File;
use App\Http\Responses\Files\FilesListResponse;
use Illuminate\Http\Request;

class FileController extends Controller
{
    public function index()
    {
        $customers = Customer::active()->pluck('name', 'id');
        return view('files.index', ['customers' => $customers]);
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


