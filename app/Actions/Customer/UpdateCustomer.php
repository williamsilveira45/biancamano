<?php

namespace App\Actions\Customer;

use App\Models\Customer;
use App\Http\Traits\Actions\ModelActionBase;
use App\Http\Traits\Actions\ResponseMessage;
use Exception;
use Illuminate\Support\Facades\Validator;

/**
 * @method bool execute(Customer $customer, array $data = [])
 * @property Customer $actionRecord
 */
class UpdateCustomer
{
    use ModelActionBase;
    use ResponseMessage;

    /**
     * @return bool|array
     * @throws Exception
     */
    protected function main()
    {
        $this->validation();
        try {
            $data = $this->request->all();

            $this->actionRecord->name = $data['name'];
            $this->actionRecord->cnpj = $data['cnpj'];

            $this->actionRecord->save();

            return redirect()->back()->with('message', 'Cliente atualizado com sucesso.');
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
            'cnpj' => ['required', 'cnpj', 'unique:customers,cnpj,'.$this->actionRecord->id, 'max:18'],
        ])->validate();
    }
}
