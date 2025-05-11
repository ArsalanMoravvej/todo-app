<?php /** @noinspection PhpMultipleClassDeclarationsInspection */

namespace App\Filters\V1;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class TodoQueryFilter
{
    protected Request $request;
    protected array $filters = [
        'status',
        'priority',
        'title',
        'description',
    ];


    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply(Builder $query): Builder
    {
        $query->where('user_id', $this->request->user()->id);
        $query_params = $this->request->query();

        if (array_key_exists('userIncluded', $query_params)) {
            $query->with('user');
        }

        foreach ($this->filters as $param) {
            if (array_key_exists($param, $query_params)) {
                $query->$param($query_params[$param]);
            }
        }
        return $query;
    }
}
