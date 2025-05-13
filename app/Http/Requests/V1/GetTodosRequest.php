<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class GetTodosRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Query parameters
            'page' => 'sometimes|integer|min:1',
            'limit' => 'sometimes|integer|between:1,100',
            'title' => 'sometimes|string|string|max:255',
            'description' => 'sometimes|string',
            'status' => 'sometimes|in:todo,in-progress,done',
            'priority' => 'sometimes|integer|between:1,3',
            'userIncluded' => 'sometimes',
        ];
    }


    public function queryParameters(): array
    {
        return [
            'page' => [
                'description' => 'The page number for pagination',
                'example' => 1,
            ],
            'limit' => [
                'description' => 'Number of items per page (max 100)',
                'example' => 15,
            ],
            'title' => [
                'description' => 'Search term to filter results by title',
                'example' => 'Pay rent',
            ],
            'description' => [
                'description' => 'Search term to filter results by description',
                'example' => 'Set appointment with',
            ],
            'status' => [
                'description' => 'Filter by the status of the item',
                'example' => 'in-progress',
            ],
            'priority' => [
                'description' => 'Filter by the priority of the item',
                'example' => 2,
            ],
            'userIncluded' => [
                'description' => 'Includes user\'s information in results if passed',
                'example' => 'true',
            ],
        ];
    }

}
