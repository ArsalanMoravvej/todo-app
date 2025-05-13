<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreTodoRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'nullable|in:todo,in-progress,done',
            'priority' => 'nullable|integer|between:1,3',
        ];
    }
    public function bodyParameters(): array
    {
        return [
            'title' => [
                'description' => 'The title of the todo item',
                'example' => 'Pay this month\'s rent',
            ],
            'description' => [
                'description' => 'Detailed description of the todo item',
                'example' => 'Contact landlord and arrange payment method',
            ],
            'status' => [
                'description' => 'Current status of the todo item (defaults to "todo" if not specified)',
                'example' => 'in-progress',
            ],
            'priority' => [
                'description' => 'Priority level from 1 (lowest) to 3 (highest)',
                'example' => 2,
            ]
        ];
    }


//    protected function prepareForValidation() {
//        $this->merge([
//            'field_name' => $this->fieldName,
//        ]);
//    }
}
