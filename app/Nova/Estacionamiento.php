<?php

namespace App\Nova;

use App\Nova\Filters\EstadoFilter;
use App\Nova\Filters\MunicipioFilter;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Number;
use Illuminate\Http\Request;

class Estacionamiento extends Resource
{
    public static $model = \App\Models\Estacionamiento::class;

    public static $title = 'nombre';

    public static $search = ['id', 'nombre'];

    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),

            Text::make('Nombre')
                ->sortable()
                ->rules('required', 'max:255'),

            Number::make('Latitud')
                ->rules('required', 'numeric'),

            Number::make('Longitud')
                ->rules('required', 'numeric'),

            Boolean::make('Activo', 'is_active'),

            Select::make('Estado', 'estado_id')
                ->options(\App\Models\Estado::pluck('nombre', 'id'))
                ->displayUsingLabels()
                ->onlyOnForms(),

            Select::make('Municipio', 'municipio_id')
                ->options(\App\Models\Municipio::pluck('nombre', 'id'))
                ->displayUsingLabels()
                ->onlyOnForms(),

            Text::make('Dirección', 'direccion')
                ->sortable()
                ->rules('nullable', 'max:255'),

            Text::make('Estado', function () {
                return $this->estado->nombre ?? '';
            })->onlyOnIndex(),

            Text::make('Municipio', function () {
                return $this->municipio->nombre ?? '';
            })->onlyOnIndex(),

        ];
    }

    public function filters(Request $request)
    {
        return [
            new EstadoFilter,
            new MunicipioFilter,
        ];
    }
}
