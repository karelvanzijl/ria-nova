<?php

namespace App\Nova\Filters;

use App\Containers\Role\Models\Role;
use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class RoleFilter extends Filter
{
    /**
     * The filter's component.
     *
     * @var string
     */
    public $component = 'select-filter';

    /**
     * Apply the filter to the given query.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Request $request, $query, $value)
    {
        $role = Role::where('id', $value)->first();
        return $query->join('model_has_roles', 'id', '=', 'model_id')->where('role_id', $role->id);
    }

    /**
     * Get the filter's available options.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function options(Request $request)
    {
        $models = Role::orderBy('level')->get();
        return $models->pluck('id', 'display_name')->all();
    }
}
