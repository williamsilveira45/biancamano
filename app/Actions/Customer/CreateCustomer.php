<?php

namespace App\Actions\Customer;

use App\Http\Traits\Actions\ActionBase;
use App\Http\Traits\Actions\ResponseInertia;
use App\Models\Customer;
use App\Models\File;
use App\Helpers\TextFormatting;
use App\Http\Traits\Actions\ModelActionBase;
use App\Http\Traits\Actions\ResponseMessage;
use App\Jobs\ParseCsvFile;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laravel\Jetstream\RedirectsActions;

/**
 * Class CreateCustomer
 * @package App\Actions\Customer
 * @property Request $request
 */
class CreateCustomer
{
    use ActionBase;

    private $validator;

    /**
     * @return bool|array
     * @throws Exception
     */
    protected function main()
    {
        $this->validation();
        try {
            $data = $this->request->all();

            Customer::create([
                'name' => $data['name'],
                'cnpj' => $data['cnpj']
            ]);

            return redirect()->back()->with('message', 'Cliente cadastrado com sucesso');

        } catch (Exception $e) {
            return redirect()->back()->with('errorMessage', $e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    private function validation()
    {
        Validator::make($this->request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'cnpj' => ['required', 'cnpj', 'unique:customers', 'max:18'],
        ])->validate();
    }
}
