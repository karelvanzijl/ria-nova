<?php

namespace App\Nova;

use Ebess\AdvancedNovaMediaLibrary\Fields\Images;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Line;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Stack;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;

class Customer extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Containers\Customer\Models\Customer::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'company';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
        'company',
    ];

    // Overwrite the indexQuery to include relationship count
    public static function indexQuery(NovaRequest $request, $query)
    {
        // Give relationship name as alias else Laravel will name it as comments_count
        return $query
            ->withCount('trails as trailsCount')
            ->withCount('users as usersCount')
            ->withCount('activeUsers as activeUsersCount');
    }

    public static function relatableUsers(NovaRequest $request, $query)
    {
        return $query->where('customer_id', $request->resourceId);
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
            Images::make('Logo', 'logo_small')->onlyOnIndex()->textAlign('center'),
            Stack::make('Company', [
                Line::make('Company')->asHeading(),
                Line::make('Owner', function () {
                    return optional($this->root)->name;
                })->asSmall(),
            ])
            ->sortable()
            ->onlyOnIndex(),
            Text::make('Company')->hideFromIndex()->sortable(),
            Number::make('Trails', 'trailsCount')->sortable()->textAlign('center')->onlyOnIndex(),
            Number::make('Users', 'usersCount')->sortable()->textAlign('center')->onlyOnIndex(),
            Number::make('Active', 'activeUsersCount')->sortable()->textAlign('center')->onlyOnIndex(),
            BelongsTo::make('Owner', 'root', User::class)->searchable()->withSubtitles()->hideFromIndex(),
            Date::make('Created At')->sortable()->onlyOnIndex(),

            new Panel('Media', $this->mediaFields()),
            new Panel('Users', $this->userFields()),
        ];
    }

    protected function mediaFields()
    {
        return [
            Images::make('Logo large', 'logo_large')->hideFromIndex(),
            Images::make('Logo small', 'logo_small')->hideFromIndex(),
            Images::make('Avatar', 'avatar')->hideFromIndex(),
            Images::make('Cover', 'cover')->hideFromIndex(),
        ];
    }

    protected function userFields()
    {
        return [
            HasMany::make('Users', 'users', User::class)->hideFromIndex(),
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
        return [];
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
