<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTodoRequest extends FormRequest
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
        if ($this->isMethod('PUT')) {
            return [
                'title'       => 'required|string|max:255',
                'description' => 'required|string',
                'status'      => 'required|nullable|in:todo,in-progress,done',
                'priority'    => 'required|nullable|integer|between:1,3',
            ];
        }
        else {
            return [
                'title'       => 'sometimes|string|max:255',
                'description' => 'sometimes|string',
                'status'      => 'sometimes|nullable|in:todo,in-progress,done',
                'priority'    => 'sometimes|nullable|integer|between:1,3',
            ];
        }
    }

    public function bodyParameters(): array
    {
        return [
            'title' => [
                'description' => 'The updated title of the todo item',
                'example' => 'Pay this month\'s rent before Friday',
                'required' => false
            ],
            'description' => [
                'description' => 'Updated detailed description of the todo item',
                'example' => 'Contact landlord before Thursday and arrange payment method for Friday',
                'required' => false
            ],
            'status' => [
                'description' => 'Updated status of the todo item',
                'example' => 'done',
                'required' => false
            ],
            'priority' => [
                'description' => 'Updated priority level from 1 (lowest) to 3 (highest)',
                'example' => 3,
                'required' => false
            ]
        ];
    }
}
