<?php

namespace App\Http\Requests;

use App\Enums\FuelType;
use App\Enums\TransmissionType;
use App\Models\CarModel;
use App\Models\Make;
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

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        $maximumVehicleYear = now()->year + 1;

        return [
            'q' => ['nullable', 'string', 'max:100'],
            'make' => ['nullable', 'string', 'required_with:model', Rule::exists('makes', 'slug')->where('is_active', true)],
            'model' => ['nullable', 'string', Rule::exists('car_models', 'slug')->where('is_active', true)],
            'body_type' => ['nullable', 'string', Rule::exists('body_types', 'slug')->where('is_active', true)],
            'year_min' => ['nullable', 'integer', 'digits:4', 'min:1900', "max:{$maximumVehicleYear}"],
            'year_max' => ['nullable', 'integer', 'digits:4', 'min:1900', "max:{$maximumVehicleYear}"],
            'price_min' => ['nullable', 'integer', 'min:0'],
            'price_max' => ['nullable', 'integer', 'min:0'],
            'transmission' => ['nullable', Rule::in(array_column(TransmissionType::cases(), 'value'))],
            'fuel_type' => ['nullable', Rule::in(array_column(FuelType::cases(), 'value'))],
            'mileage_min' => ['nullable', 'integer', 'min:0'],
            'mileage_max' => ['nullable', 'integer', 'min:0'],
            'car_stand' => ['nullable', 'string', Rule::exists('car_stands', 'slug')->where('is_active', true)],
            'availability' => ['nullable', Rule::in(['available', 'reserved'])],
            'sort' => ['nullable', Rule::in(['latest', 'price_asc', 'price_desc', 'year_desc', 'mileage_asc'])],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    /** @return array<int, callable(Validator): void> */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $this->validateRange($validator, 'year_min', 'year_max', 'The minimum year cannot be greater than the maximum year.');
                $this->validateRange($validator, 'price_min', 'price_max', 'The minimum price cannot be greater than the maximum price.');
                $this->validateRange($validator, 'mileage_min', 'mileage_max', 'The minimum mileage cannot be greater than the maximum mileage.');
                $this->validateModelOwnership($validator);
            },
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'make.required_with' => 'Select a make before choosing a model.',
            'q.max' => 'The keyword search cannot be longer than 100 characters.',
            'year_min.digits' => 'The minimum year must contain four digits.',
            'year_max.digits' => 'The maximum year must contain four digits.',
            '*.exists' => 'The selected :attribute is not available.',
            '*.in' => 'The selected :attribute is not valid.',
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'body_type' => 'body type',
            'fuel_type' => 'fuel type',
            'car_stand' => 'car stand',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('q')) {
            $this->merge(['q' => trim((string) $this->input('q'))]);
        }
    }

    private function validateRange(Validator $validator, string $minimumKey, string $maximumKey, string $message): void
    {
        if ($validator->errors()->hasAny([$minimumKey, $maximumKey])) {
            return;
        }

        $minimum = $this->input($minimumKey);
        $maximum = $this->input($maximumKey);

        if ($minimum !== null && $minimum !== '' && $maximum !== null && $maximum !== '' && (int) $minimum > (int) $maximum) {
            $validator->errors()->add($maximumKey, $message);
        }
    }

    private function validateModelOwnership(Validator $validator): void
    {
        if ($validator->errors()->hasAny(['make', 'model']) || ! $this->filled('model')) {
            return;
        }

        $make = Make::query()->where('slug', (string) $this->string('make'))->where('is_active', true)->first();
        $modelBelongsToMake = $make !== null && CarModel::query()
            ->where('slug', (string) $this->string('model'))
            ->where('make_id', $make->id)
            ->where('is_active', true)
            ->exists();

        if (! $modelBelongsToMake) {
            $validator->errors()->add('model', 'The selected model does not belong to the selected make.');
        }
    }
}
