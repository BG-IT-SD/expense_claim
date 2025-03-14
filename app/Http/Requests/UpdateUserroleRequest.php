<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserroleRequest extends FormRequest
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
            'userid' => [
                'required',
                Rule::unique('user_roles')->where(function ($query) {
                    return $query->where('moduleid', request('moduleid'))
                    ->where('deleted', 0);
                })->ignore($this->route('UserRole')) // Ensure parameter name matches route
            ],
            'moduleid' => 'required',
            'roleid' => 'required',
            'status' => 'required'
        ];
    }

    /**
     * Custom error messages for validation.
     */
    public function messages(): array
    {
        return [
            'userid.unique' => 'มีสิทธิการใช้งานนี้อยู่แล้วกรุณาตรวจสอบ',
            'moduleid.required' => 'กรุณาเลือกหน้าจอ',
            'roleid.required' => 'กรุณาเลือกสิทธิ',
            'status.required' => 'กรุณาเลือกสถานะ',
        ];
    }
}
