<?php

namespace App\Imports;

use App\Models\GroupSpecial;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Validators\Failure;

class GroupSpecialImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    public $importResults = []; // Store success & error messages

    public function model(array $row)
    {
        try {
            $user = GroupSpecial::create([
                'typeid'   => $row['typeid'] ?? null,
                'empid'    => $row['empid'] ?? null,
                'fullname' => $row['fullname'] ?? null,
                'position' => $row['position'] ?? null,
            ]);

            $this->importResults[] = [
                'row'     => $row,
                'status'  => 'success',
                'message' => 'User imported successfully'
            ];
        } catch (\Exception $e) {
            $this->importResults[] = [
                'row'     => $row,
                'status'  => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    public function rules(): array
    {
        return [
            'typeid'   => 'required|integer',
            'empid'    => ['required', Rule::unique('group_specials', 'empid')],
            'fullname' => 'required|string|max:255',
            'position' => 'required|string|max:255',
        ];
    }

    // Capture validation errors for each row
    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->importResults[] = [
                'row'     => $failure->values(),
                'status'  => 'error',
                'message' => 'Row ' . $failure->row() . ': ' . implode(', ', $failure->errors())
            ];
        }
    }
}
