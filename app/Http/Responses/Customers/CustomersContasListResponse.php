<?php

namespace App\Http\Responses\Customers;
use App\Models\Customer;
use App\Models\CustomerContas;
use Illuminate\Contracts\Support\Responsable;

class CustomersContasListResponse implements Responsable
{
    /**
     * @param $request
     * @return mixed
     */
    public function toResponse($request)
    {
        $sort = empty($request->input('sort'))
            ? explode('|', 'nome_csv|asc')
            : explode('|', $request->input('sort'));
        $perpage = $request->input('per_page');

        if ($request->filled('search')) {
            return $this->search($request->input('search'), $perpage, $sort);
        }

        return $this->getList($perpage, $sort);
    }

    /**
     * @param $perpage
     * @param $sort
     * @return mixed
     */
    protected function getList($perpage, $sort)
    {
        return CustomerContas::select([
                'customer_contas.id',
                'customer_contas.customer_id',
                'customer_contas.conta_id',
                'customer_contas.nome_csv',
                'customer_contas.nome_sha1',
                'customer_contas.active',
                'customer_contas.created_at',
                'customer_contas.updated_at',
                'plano_contas.nome_conta',
            ])
            ->join('plano_contas', 'plano_contas.id', '=', 'customer_contas.conta_id')
            ->orderBy($sort[0], $sort[1])
            ->paginate($perpage);
    }

    /**
     * @param $search
     * @param $perpage
     * @param $sort
     * @return mixed
     */
    protected function search($search, $perpage, $sort)
    {
        return CustomerContas::select([
                'customer_contas.id',
                'customer_contas.customer_id',
                'customer_contas.conta_id',
                'customer_contas.nome_csv',
                'customer_contas.nome_sha1',
                'customer_contas.active',
                'customer_contas.created_at',
                'customer_contas.updated_at',
                'plano_contas.nome_conta',
            ])
            ->join('plano_contas', 'plano_contas.id', '=', 'customer_contas.conta_id')
            ->where('id', $search)
            ->orWhere('nome_csv', 'LIKE', '%'.$search.'%')
            ->orderBy($sort[0], $sort[1])
            ->paginate($perpage);
    }
}
