<?php

namespace App\Nova;

use Ebess\AdvancedNovaMediaLibrary\Fields\Images;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Line;
use Laravel\Nova\Fields\MorphToMany;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Stack;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Panel;
use Illuminate\Support\Str;

class User extends Resource
{
    public static $with = ['customer'];

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Containers\User\Models\User::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'firstname';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'firstname', 'lastname', 'email',
    ];

    public function title()
    {
        return implode(' ', [$this->firstname, $this->lastname]);
    }

    public function subtitle()
    {
        return "Company: {$this->customer->company}";
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make(__('ID'), 'id')->hideFromIndex()->sortable(),

            Images::make('Avatar', 'avatar')->onlyOnIndex(),

            BelongsTo::make('Customer')->hideFromIndex(),

            Stack::make('Name', [
                Line::make('Name')->displayUsing(function ($value) {
                    return Str::limit($value, 25, '...');
                })->asHeading(),
                Line::make('Customer', function () {
                    return optional($this->customer)->company;
                })->asSmall(),
            ])
            ->sortable()
            ->onlyOnIndex(),

            Select::make('Gender', 'gender')
                ->options([
                    'Male',
                    'Female',
                    'Non-Binary'
                ])
                ->sortable()
                ->displayUsingLabels()
                ->hideFromIndex(),

            Text::make('Firstname')
                ->sortable()
                ->hideFromIndex()
                ->rules('required', 'max:255'),

            Text::make('Lastname')
                ->sortable()
                ->hideFromIndex()
                ->rules('required', 'max:255'),

            Stack::make('Role', [
                Line::make('Role', 'highestRole', function () {
                    return $this->highestRole->first()->display_name;
                })->asSubTitle(),
                Line::make('Roles', function () {
                    return implode(', ', array_filter($this->roles()->orderByDesc('level')->pluck('display_name')->toArray(), function ($v, $k) {
                        return $v != $this->highestRole->first()->display_name;
                    }, ARRAY_FILTER_USE_BOTH));
                })->asSmall(),
            ])
                ->onlyOnIndex(),

            Text::make('Role', function () {
                return $this->highestRole->first()->display_name;
            })->hideFromIndex(),

            Text::make('Email')->displayUsing(function ($value) {
                return Str::limit($value, 25, '...');
            })
                ->sortable()
                ->onlyOnIndex(),

            Text::make('Email')->displayUsing(function ($value) {
                return Str::limit($value, 25, '...');
            })
                ->sortable()
                ->rules('required', 'email', 'max:254')
                ->creationRules('unique:users,email')
                ->updateRules('unique:users,email,{{resourceId}}')
                ->hideFromIndex(),

            Date::make('Created At')->onlyOnIndex()->sortable(),

            Password::make('Password')
                ->onlyOnForms()
                ->creationRules('required', 'string', 'min:8')
                ->updateRules('nullable', 'string', 'min:8'),

            Date::make('Birthdate', 'birth')
                ->sortable()
                ->hideFromIndex(),

            Select::make('Experience', 'experience')
                ->options([
                    'Less than 1 year',
                    '1 to 2 years',
                    '3 to 5 years',
                    '6 to 10 years',
                    '11 to 15 years',
                    '15 to 20 years',
                    'More than 20 years'
                ])
                ->sortable()
                ->displayUsingLabels()
                ->hideFromIndex(),

            Select::make('Education', 'education')
                ->options([
                    'Below secondary',
                    'Secondary',
                    'Trade / technical / vocational',
                    'Associate / College',
                    'Bachelor',
                    'Master / Professional', 'Doctoral'
                ])
                ->sortable()
                ->displayUsingLabels()
                ->hideFromIndex(),

            Select::make('Country', 'country')
                ->options(config('ria_countries'))
                ->sortable()
                ->displayUsingLabels()
                ->searchable()
                ->hideFromIndex(),

            Select::make('Nationality', 'nationality')
                ->options(config('ria_countries'))
                ->sortable()
                ->displayUsingLabels()
                ->searchable()
                ->hideFromIndex(),

            Select::make('Language', 'language')
                ->options(config('ria_languages'))
                ->sortable()
                ->displayUsingLabels()
                ->searchable()
                ->hideFromIndex(),

            MorphToMany::make('Roles'),

            new Panel('Media', $this->mediaFields()),
        ];
    }

    protected function mediaFields()
    {
        return [
            Images::make('Avatar', 'avatar')->hideFromIndex(),
            Images::make('Cover', 'cover')->hideFromIndex(),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [
            new Filters\RoleFilter,
        ];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
