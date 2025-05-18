<?php

namespace App\Nova\Filters;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;
use App\Models\Estado;

class EstadoFilter extends Filter
{
    public $name = 'Filtrar por Estado';

    public function apply(Request $request, $query, $value)
    {
        return $query->where('estado_id', $value);
    }

    public function options(Request $request)
    {
        return Estado::orderBy('nombre')->pluck('id', 'nombre')->toArray();
    }
}
