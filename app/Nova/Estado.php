<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Illuminate\Http\Request;
use Laravel\Nova\Resource;

class Estado extends Resource
{
    public static $model = \App\Models\Estado::class;

    public static $title = 'nombre';

    public static $search = ['id', 'nombre'];

    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),

            Text::make('Nombre')
                ->sortable()
                ->rules('required', 'max:255'),
        ];
    }
}