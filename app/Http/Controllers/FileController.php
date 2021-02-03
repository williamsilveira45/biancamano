<?php

namespace App\Http\Controllers;

use App\Actions\File\Upload;
use App\Jobs\DeleteFile;
use App\Models\Customer;
use App\Http\Responses\Files\FilesListResponse;
use App\Models\File;
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
        foreach ($customers as $id => $c) {
            $cc[] = ['label' => $c, 'code' => $id];
        }

        return Inertia::render('Files/Show', ['customers' => $cc, 'csrf' => csrf_token()]);
    }

    /**
     * @return FilesListResponse
     */
    public function json()
    {
        return new FilesListResponse();
    }

    /**
     * @param Request $request
     * @return bool
     * @throws \Exception
     */
    public function upload(Request $request)
    {
        $customer = Customer::findOrFail($request->input('customer_id'));
        return (new Upload())->execute($customer, [
            'file' => $request->file('file')
        ]);
    }

    /**
     * @param File $file
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(File $file)
    {
        dispatch(new DeleteFile($file))->onQueue('long');

        return response()->json([
            'type' => 'success',
            'success' => true,
            'message' => 'Iniciando o processo para deletar o arquivo e recalcular valores.'
        ]);

    }
}


