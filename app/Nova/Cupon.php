<?php

namespace App\Nova;

use App\Nova\Estacionamiento;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Date;
use Illuminate\Validation\Rule;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Resource as NovaResource;

class Cupon extends NovaResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Cupon>
     */
    public static $model = \App\Models\Cupon::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'nombre';


    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
        'nombre'
    ];
    public static function label()
    {
        return 'Cupones';
    }
    public static function singularLabel()
    {
        return 'Cupon';
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @return array<int, \Laravel\Nova\Fields\Field>
     */
    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),
            Text::make('Código', 'nombre')
                ->rules(
                    'required',
                    Rule::unique('cupones', 'nombre')->ignore($this->id)
                )
                ->displayUsing(fn($value) => strtoupper($value)),
            Select::make('Descuento')
                ->options([
                    'parcial' => 'Parcial',
                    'gratis' => 'Gratis',
                ])
                ->default('parcial')
                ->displayUsingLabels()
                ->rules('required')
                ->fillUsing(function ($request, $model, $attribute, $requestAttribute) {
                    $model->$attribute = $request->$requestAttribute;

                    if ($request->$requestAttribute === 'gratis') {
                        $model->monto = null;
                    }
                }),
            Number::make('Monto')
                ->help('Solo requerido si el descuento es parcial.')
                ->rules(function (Request $request) {
                    return [
                        $request->get('descuento') === 'parcial' ? 'required' : 'nullable',
                        'numeric',
                        'min:0',
                    ];
                }),
            BelongsTo::make('Estacionamiento', 'estacionamiento', Estacionamiento::class)->rules('required'),
            Date::make('Válido Hasta', 'valido_hasta')->nullable(),
            Boolean::make('Activo'),
            BelongsTo::make('Usuario', 'user', User::class)->exceptOnForms(),
        ];
    }

    /**
     * Get the cards available for the resource.
     *
     * @return array<int, \Laravel\Nova\Card>
     */
    public function cards(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @return array<int, \Laravel\Nova\Filters\Filter>
     */
    public function filters(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @return array<int, \Laravel\Nova\Lenses\Lens>
     */
    public function lenses(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @return array<int, \Laravel\Nova\Actions\Action>
     */
    public function actions(NovaRequest $request): array
    {
        return [];
    }

    public static function fill(NovaRequest $request, $model): array
    {
        $result = parent::fill($request, $model);

        if ($request->isCreateOrAttachRequest()) {
            $result[0]->user_id = $request->user()->id;
        }

        return $result;
    }
}
