<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGroupTypeRequest extends FormRequest
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
        $rules = [
            'groupname' => [
                'required',
                Rule::unique('typegroups', 'groupname')->where(fn ($query) => $query->where('deleted', 0)),
            ],
            'status' => 'required',
        ];

        // If it's an update (edit), ignore the current record's groupname
        if ($this->isMethod('patch') || $this->isMethod('put')) {
            $rules['groupname'] = [
                'required',
                Rule::unique('typegroups', 'groupname')
                    ->ignore($this->route('Typegroup')) // Ignore the current record when updating
                    ->where(fn ($query) => $query->where('deleted', 0)), // Apply condition where deleted = 0
            ];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'groupname.required' => 'กรุณาเลือกประเภทกลุ่ม',
            'groupname.unique' => 'มีชื่อนี้อยู่แล้วกรุณาตรวจสอบ',
            'status.required' => 'กรุณาเลือกสถานะ',
        ];
    }
}
