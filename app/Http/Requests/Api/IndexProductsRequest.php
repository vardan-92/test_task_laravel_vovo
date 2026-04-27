<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class IndexProductsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'q' => ['sometimes', 'nullable', 'string', 'max:255'],
            'price_from' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'price_to' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'category_id' => ['sometimes', 'nullable', 'integer', 'exists:categories,id'],
            'in_stock' => ['sometimes', 'nullable', Rule::in(['true', 'false', '1', '0'])],
            'rating_from' => ['sometimes', 'nullable', 'numeric', 'between:0,5'],
            'sort' => ['sometimes', 'nullable', Rule::in(['price_asc', 'price_desc', 'rating_desc', 'newest'])],
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $q = $this->input('q');

        $this->merge([
            'q' => is_string($q) ? trim($q) : $q,
        ]);

        foreach (['q', 'price_from', 'price_to', 'category_id', 'in_stock', 'rating_from', 'sort', 'page', 'per_page'] as $field) {
            if ($this->has($field) && $this->input($field) === '') {
                $this->merge([$field => null]);
            }
        }
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $from = $this->input('price_from');
            $to = $this->input('price_to');

            if ($from === null || $to === null) {
                return;
            }

            if ((float)$from > (float)$to) {
                $validator->errors()->add('price_from', 'The price_from field must be less than or equal to price_to.');
            }
        });
    }

    public function filters(): array
    {
        $validated = $this->validated();

        if (array_key_exists('in_stock', $validated) && $validated['in_stock'] !== null) {
            $validated['in_stock'] = match ((string)$validated['in_stock']) {
                'true', '1' => true,
                'false', '0' => false,
                default => null,
            };
        }

        return $validated;
    }
}
