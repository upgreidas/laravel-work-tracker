<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'assignee_id' => 'nullable|exists:users,id',
            'project_id' => 'nullable|exists:projects,id',
            'name' => 'filled|max:100',
            'description' => 'max:1000',
            'due_date' => 'date',
            'status' => 'in:pending,in_progress,completed,rejected',
        ];
    }
}
