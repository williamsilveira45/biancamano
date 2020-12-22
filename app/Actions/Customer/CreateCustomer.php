<?php

namespace App\Actions\Customer;

use App\Http\Traits\Actions\ActionBase;
use App\Models\Customer;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
                'name' => strtoupper($data['name']),
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
