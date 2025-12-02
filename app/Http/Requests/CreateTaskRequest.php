<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'deadline' => ['required', 'date', 'after_or_equal:today'],
            'assigned_user_id' => ['nullable', 'exists:users,id'],
            'status' => ['sometimes', 'in:pending,in_progress,completed,blocked'],
        ];
    }
}
