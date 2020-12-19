<?php

namespace App\Http\Responses\Files;
use App\Models\File;
use Illuminate\Contracts\Support\Responsable;

class FilesListResponse implements Responsable
{
    /**
     * @param $request
     * @return mixed
     */
    public function toResponse($request)
    {
        $sort = empty($request->input('sort'))
            ? explode('|', 'name|asc')
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
        return File::orderBy($sort[0], $sort[1])
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
        return File::where('id', $search)
            ->orWhere('name', 'LIKE', '%'.$search.'%')
            ->orderBy($sort[0], $sort[1])
            ->paginate($perpage);
    }
}
