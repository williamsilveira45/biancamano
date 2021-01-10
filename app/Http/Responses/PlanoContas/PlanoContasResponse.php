<?php

namespace App\Http\Responses\PlanoContas;
use App\Models\PlanoConta;
use Illuminate\Contracts\Support\Responsable;

class PlanoContasResponse implements Responsable
{
    /**
     * @param $request
     * @return mixed
     */
    public function toResponse($request)
    {
        $sort = empty($request->input('sort'))
            ? explode('|', 'nome_conta|asc')
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
        return PlanoConta::orderBy($sort[0], $sort[1])
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
        return PlanoConta::where('id', $search)
            ->orWhere('nome_conta', 'LIKE', '%'.$search.'%')
            ->orderBy($sort[0], $sort[1])
            ->paginate($perpage);
    }
}
