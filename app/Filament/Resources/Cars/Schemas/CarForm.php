<?php

namespace App\Filament\Resources\Cars\Schemas;

use App\Enums\DrivetrainType;
use App\Enums\FuelType;
use App\Enums\MileageUnit;
use App\Enums\TransmissionType;
use App\Enums\VehicleCondition;
use App\Models\CarModel;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class CarForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('Vehicle')->columnSpanFull()->tabs([
                Tab::make('Identity')->schema([
                    Section::make('Inventory identity')->columns(2)->schema([
                        TextInput::make('stock_number')->disabled()->dehydrated(false)->placeholder('Generated when saved'),
                        TextInput::make('slug')->disabled()->dehydrated(false)->placeholder('Generated when saved'),
                        Select::make('make_id')->relationship('make', 'name', modifyQueryUsing: fn (Builder $query) => $query->orderBy('name'))->searchable()->preload()->live()->required(),
                        Select::make('car_model_id')->label('Model')->options(fn ($get): array => CarModel::query()->where('make_id', $get('make_id'))->orderBy('name')->pluck('name', 'id')->all())->searchable()->required(),
                        Select::make('body_type_id')->relationship('bodyType', 'name', modifyQueryUsing: fn (Builder $query) => $query->orderBy('name'))->searchable()->preload()->required(),
                        Select::make('car_stand_id')->label('Car stand')->relationship('carStand', 'name', modifyQueryUsing: fn (Builder $query) => $query->orderBy('name'))->searchable()->preload()->required(),
                        TextInput::make('trim')->maxLength(255),
                        TextInput::make('year')->numeric()->minValue(1900)->maxValue((int) date('Y') + 1)->required(),
                    ]),
                ]),
                Tab::make('Pricing & specifications')->schema([
                    Section::make('Pricing')->columns(3)->schema([
                        TextInput::make('price_amount')->label('Price')->numeric()->prefix('₦')->minValue(0)->required(),
                        TextInput::make('currency')->default((string) config('automercy.currency'))->disabled()->dehydrated(),
                        Toggle::make('is_featured')->label('Featured inventory'),
                    ]),
                    Section::make('Specifications')->columns(3)->schema([
                        TextInput::make('mileage')->numeric()->minValue(0)->required(),
                        Select::make('mileage_unit')->options(self::options(MileageUnit::cases()))->required(),
                        Select::make('condition')->options(self::options(VehicleCondition::cases()))->required(),
                        Select::make('transmission')->options(self::options(TransmissionType::cases()))->required(),
                        Select::make('fuel_type')->options(self::options(FuelType::cases()))->required(),
                        Select::make('drivetrain')->options(self::options(DrivetrainType::cases()))->required(),
                        TextInput::make('engine')->maxLength(255),
                        TextInput::make('exterior_colour')->label('Exterior colour')->maxLength(255),
                        TextInput::make('interior_colour')->label('Interior colour')->maxLength(255),
                    ]),
                    Select::make('features')->relationship('features', 'name', modifyQueryUsing: fn (Builder $query) => $query->orderBy('category')->orderBy('name'))->multiple()->searchable()->preload(),
                    KeyValue::make('supplemental_specs')->label('Additional specifications')->keyLabel('Specification')->valueLabel('Value'),
                ]),
                Tab::make('Content')->schema([
                    RichEditor::make('description')->required()->columnSpanFull(),
                    TextInput::make('video_url')->label('Video URL')->url()->maxLength(2048),
                ]),
                Tab::make('SEO')->schema([
                    TextInput::make('meta_title')->maxLength(70),
                    Textarea::make('meta_description')->maxLength(170)->rows(3),
                    TextInput::make('canonical_override')->label('Canonical URL override')->url()->maxLength(2048),
                ]),
            ]),
        ]);
    }

    /** @param array<int, object> $cases */
    private static function options(array $cases): array
    {
        return collect($cases)->mapWithKeys(fn ($case): array => [$case->value => $case->label()])->all();
    }
}
