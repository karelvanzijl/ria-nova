<?php

namespace App\Nova;

use Ebess\AdvancedNovaMediaLibrary\Fields\Images;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Line;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Stack;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Trail extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Containers\Trail\Models\Trail::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
        'name',
        'node_type',
    ];

    /**
     * The columns that should be searched in relationships.
     *
     * @var array
     */
    public static $searchRelations = [
        'customer' => ['company'],
        'creator' => ['firstname', 'lastname'],
    ];

    public static function authorizedToCreate(Request $request)
    {
        return false;
    }

    public function subtitle()
    {
        return "{$this->customer->company} | {$this->creator->name}";
    }

    // Overwrite the indexQuery to include relationship count
    public static function indexQuery(NovaRequest $request, $query)
    {
        // Give relationship name as alias else Laravel will name it as comments_count
        return $query
            ->whereNull('parent_id')
            ->where('level', 0)
            ->withCount('users as usersCount');
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

            Images::make('Icon', 'icon'),

            Stack::make('Name', [
                Line::make('Name')->asHeading(),
                Line::make('Customer', function () {
                    return optional($this->customer)->company . ' | ' . optional($this->creator)->name;
                })->asSmall(),
            ])
            ->sortable()
            ->onlyOnIndex(),

            Text::make('Name')->hideFromIndex()->sortable(),

            Select::make('Type', 'node_type')
                ->options([
                    'default' => 'Default',
                    'exam' => 'Exam',
                    'survey' => 'Survey',
                    'battle' => 'Battle',
                    'assessment' => 'Assessment',
                ])
                ->sortable()
                ->displayUsingLabels()
                ->onlyOnIndex(),

            Number::make('Players', 'usersCount')->sortable()->onlyOnIndex()->textAlign('center'),

            Boolean::make('Active', 'is_active')->sortable(),

            Boolean::make('Public', 'is_public')->sortable()->readonly($this->node_type === 'battle'),

            // Date::make('Created At')->sortable(),

            BelongsTo::make('Customer')->searchable()->readonly()->hideFromIndex(),

            BelongsTo::make('Creator', 'creator', User::class)->hideFromIndex()->searchable()->withSubtitles(),
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
            new Filters\CustomerFilter,
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
