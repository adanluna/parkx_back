<?php

namespace App\Nova\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;

class ResetAppUserPassword extends Action
{
    use Queueable;

    public $name = 'Resetear contraseña';

    public function handle(ActionFields $fields, Collection $models)
    {
        foreach ($models as $user) {
            $user->update([
                'password' => Hash::make($fields->new_password),
            ]);
        }

        return Action::message('Contraseña actualizada correctamente.');
    }

    public function fields(NovaRequest $request)
    {
        return [
            Password::make('Nueva contraseña', 'new_password')
                ->rules('required', 'min:6')
                ->onlyOnForms(),
        ];
    }
}
