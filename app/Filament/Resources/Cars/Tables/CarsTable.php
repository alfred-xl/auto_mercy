<?php

namespace App\Filament\Resources\Cars\Tables;

use App\Enums\CarStatus;
use App\Enums\ListingCategory;
use App\Filament\Resources\Cars\Actions\CarDeleteAction;
use App\Filament\Resources\Cars\Actions\CarLifecycleActions;
use App\Models\Car;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CarsTable
{
    public static function configure(Table $table): Table
    {
        return $table->defaultSort('created_at', 'desc')->columns([
            Split::make([
                Split::make([
                    ImageColumn::make('coverImage.path')
                        ->state(fn (Car $record): ?string => $record->coverImage?->variantPath('thumbnail'))
                        ->disk((string) config('automercy.media.disk'))
                        ->square()
                        ->size(72)
                        ->grow(false)
                        ->defaultImageUrl(asset('images/auto-mercy-logo.webp')),
                    Stack::make([
                        tap(
                            TextColumn::make('vehicle')->state(fn (Car $record): string => trim("{$record->make} {$record->model} {$record->trim}"))->description(fn (Car $record): string => (string) $record->year)->searchable(query: fn (Builder $query, string $search): Builder => $query->where(fn (Builder $query) => $query->where('make', 'like', "%{$search}%")->orWhere('model', 'like', "%{$search}%")->orWhere('trim', 'like', "%{$search}%")))->weight('semibold'),
                            fn (TextColumn $column): TextColumn => $column
                                ->weight(FontWeight::SemiBold)
                                ->size(TextSize::Large)
                                ->wrap(),
                        ),
                        TextColumn::make('price_amount')
                            ->money('NGN')
                            ->sortable()
                            ->color('primary')
                            ->weight(FontWeight::Bold)
                            ->size(TextSize::Large),
                    ])->space(1),
                ]),
                Stack::make([
                    Split::make([
                        TextColumn::make('listing_category')->badge()->sortable()->grow(false),
                        TextColumn::make('status')->badge()->sortable()->grow(false),
                        TextColumn::make('is_featured')
                            ->state(fn (Car $record): ?string => $record->is_featured ? 'Featured' : null)
                            ->badge()
                            ->icon('heroicon-m-star')
                            ->color('warning')
                            ->placeholder('')
                            ->grow(false),
                    ]),
                    TextColumn::make('created_at')
                        ->date('d M Y')
                        ->sortable()
                        ->icon('heroicon-m-calendar-days')
                        ->color('gray')
                        ->size(TextSize::ExtraSmall),
                ])->space(2)->grow(false),
            ])->from('md'),
        ])->filters([
            SelectFilter::make('listing_category')->options(collect(ListingCategory::cases())->mapWithKeys(fn (ListingCategory $category): array => [$category->value => $category->label()])->all()),
            SelectFilter::make('status')->options(collect(CarStatus::cases())->mapWithKeys(fn (CarStatus $status): array => [$status->value => $status->label()])->all()),
            SelectFilter::make('make')->options(fn (): array => Car::query()->distinct()->orderBy('make')->pluck('make', 'make')->all())->searchable(),
            SelectFilter::make('model')->options(fn (): array => Car::query()->distinct()->orderBy('model')->pluck('model', 'model')->all())->searchable(),
            SelectFilter::make('body_type')->options(fn (): array => Car::query()->whereNotNull('body_type')->distinct()->orderBy('body_type')->pluck('body_type', 'body_type')->all()),
            Filter::make('year')->schema([TextInput::make('from')->numeric(), TextInput::make('to')->numeric()])->query(fn (Builder $query, array $data): Builder => $query->when($data['from'] ?? null, fn (Builder $query, $year) => $query->where('year', '>=', $year))->when($data['to'] ?? null, fn (Builder $query, $year) => $query->where('year', '<=', $year))),
            Filter::make('price')->schema([TextInput::make('minimum')->numeric(), TextInput::make('maximum')->numeric()])->query(fn (Builder $query, array $data): Builder => $query->when($data['minimum'] ?? null, fn (Builder $query, $price) => $query->where('price_amount', '>=', $price))->when($data['maximum'] ?? null, fn (Builder $query, $price) => $query->where('price_amount', '<=', $price))),
            TernaryFilter::make('is_featured')->label('Featured'),
        ])->recordActions([
            ActionGroup::make([ViewAction::make(), EditAction::make(), ...CarLifecycleActions::make(), CarDeleteAction::make()])->icon('heroicon-m-ellipsis-vertical')->iconButton()->tooltip('Actions'),
        ])->recordActionsAlignment('end')->contentGrid(['md' => 1])->emptyStateHeading('No vehicles yet')->emptyStateDescription('Create a draft vehicle to begin building inventory.');
    }
}
