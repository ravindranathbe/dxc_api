<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class EmployeeProcessRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'previous_year_file' => 'sometimes|file|mimes:csv,txt',
            'employees_file' => 'required|file|mimes:csv,txt',
        ];
    }

    public function messages(): array
    {
        return [
            'previous_year_file.required' => 'Please upload previous year file',
            'employees_file.required' => 'Please upload employees file',
            'previous_year_file.file' => 'Previous year file must be a file',
            'employees_file.file' => 'Employees file must be a file',
            'previous_year_file.mimes' => 'Previous year file must be a csv file',
            'employees_file.mimes' => 'Employees file must be a csv file',
        ];
    }
}
