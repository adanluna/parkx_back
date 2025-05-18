<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\BelongsTo;
use Illuminate\Http\Request;

class Municipio extends Resource
{
    public static $model = \App\Models\Municipio::class;

    public static $title = 'nombre';

    public static $search = ['id', 'nombre'];

    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),

            Text::make('Nombre')
                ->sortable()
                ->rules('required', 'max:255'),

            BelongsTo::make('Estado')
                ->rules('required'),
        ];
    }
}