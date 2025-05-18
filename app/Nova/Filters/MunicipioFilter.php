<?php

namespace App\Nova\Filters;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;
use App\Models\Municipio;

class MunicipioFilter extends Filter
{
    public $name = 'Filtrar por Municipio';

    public function apply(Request $request, $query, $value)
    {
        return $query->where('municipio_id', $value);
    }

    public function options(Request $request)
    {
        return Municipio::orderBy('nombre')->pluck('id', 'nombre')->toArray();
    }
}
