<?php

namespace App\Imports\Purchases\Sheets;

use App\Models\Purchase\BillHistory as Model;
use App\Http\Requests\Purchase\BillHistory as Request;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;

class BillHistories implements ToModel, WithHeadingRow, WithMapping, WithValidation
{
    public function model(array $row)
    {
        return new Model($row);
    }

    public function map($row): array
    {
        $row['company_id'] = session('company_id');

        return $row;
    }

    public function rules(): array
    {
        return (new Request())->rules();
    }

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $message = trans('messages.error.import_column', [
                'message' => $failure->errors()->first(),
                'sheet' => 'bill_histories',
                'line' => $failure->attribute(),
            ]);
    
            flash($message)->error()->important();
       }
    }
}