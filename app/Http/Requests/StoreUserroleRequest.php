<?php

namespace App\Http\Requests;

use App\Models\Userrole;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserroleRequest extends FormRequest
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
                function ($attribute, $value, $fail) {
                    if (Userrole::where('userid', request('userid'))
                        ->where('moduleid', request('moduleid'))
                        // ->where('roleid', request('roleid'))
                        ->where('deleted','0')
                        ->exists()) {
                        $fail('สิทธิการใช้งานนี้มีอยู่แล้วกรุณาตรวจสอบ');
                    }
                },
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
            'moduleid.required' => 'กรุณาเลือกหน้าจอ',
            'roleid.required' => 'กรุณาเลือกสิทธิ',
            'status.required' => 'กรุณาเลือกสถานะ',
        ];
    }
}
