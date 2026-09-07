<?php

namespace App\Http\Requests;

use App\Enums\CarStatus;
use App\Enums\FuelType;
use App\Enums\ListingCategory;
use App\Enums\TransmissionType;
use App\Queries\CarInventoryQuery;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class InventoryFilterRequest extends FormRequest
{
    protected $redirectRoute = 'cars.index';

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:100'],
            'listing_category' => ['nullable', Rule::enum(ListingCategory::class)],
            'make' => ['nullable', 'string', 'max:100'],
            'model' => ['nullable', 'string', 'max:100'],
            'body_type' => ['nullable', 'string', 'max:100'],
            'year_min' => ['nullable', 'integer', 'digits:4', 'min:1900', 'max:'.(now()->year + 1)],
            'year_max' => ['nullable', 'integer', 'digits:4', 'min:1900', 'max:'.(now()->year + 1)],
            'price_min' => ['nullable', 'integer', 'min:0'],
            'price_max' => ['nullable', 'integer', 'min:0'],
            'transmission' => ['nullable', Rule::enum(TransmissionType::class)],
            'fuel_type' => ['nullable', Rule::enum(FuelType::class)],
            'availability' => ['nullable', Rule::in([CarStatus::Available->value, CarStatus::Reserved->value])],
            'sort' => ['nullable', Rule::in(array_keys(CarInventoryQuery::SORT_OPTIONS))],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            foreach ([['year_min', 'year_max'], ['price_min', 'price_max']] as [$minimum, $maximum]) {
                if ($this->filled($minimum) && $this->filled($maximum) && (int) $this->input($minimum) > (int) $this->input($maximum)) {
                    $validator->errors()->add($maximum, 'The maximum value must be greater than or equal to the minimum value.');
                }
            }
        }];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('q')) {
            $this->merge(['q' => trim((string) $this->input('q'))]);
        }
    }
}
