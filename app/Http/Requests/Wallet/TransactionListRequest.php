<?php

namespace App\Http\Requests\Wallet;

use Illuminate\Foundation\Http\FormRequest;
use App\Services\Wallet\DTO\TransactionQuery;

class TransactionListRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'page'     => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:1000'],
            'order'    => ['sometimes', 'in:asc,desc'],
        ];
    }

    public function toQuery(): TransactionQuery
    {
        return new TransactionQuery(
            page: $this->input('page', 1),
            perPage: $this->input('per_page', 10),
            order: $this->input('order', 'desc')
        );
    }
}
