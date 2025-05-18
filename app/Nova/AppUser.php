<?php

namespace App\Nova;

use App\Nova\Actions\ResetAppUserPassword;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;
use Illuminate\Http\Request;
use Laravel\Nova\Resource;

class AppUser extends Resource
{
    public static $model = \App\Models\AppUser::class;

    public static $title = 'email';

    public static $search = ['id', 'name', 'email', 'apellidos'];

    public static function label()
    {
        return 'Usuarios App';
    }

    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),

            Text::make('Nombre', 'name')
                ->sortable()
                ->rules('required', 'max:255'),

            Text::make('Apellidos', 'apellidos')
                ->sortable(),

            Text::make('Email')->sortable()->rules('required', 'email'),

            Boolean::make('Verificado', 'is_verified'),

            DateTime::make('Creado', 'created_at')->onlyOnDetail(),
            DateTime::make('Actualizado', 'updated_at')->onlyOnDetail(),
        ];
    }

    public function actions(\Illuminate\Http\Request $request)
    {
        return [
            new ResetAppUserPassword
        ];
    }
}
