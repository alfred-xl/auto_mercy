<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Cars\CarResource;
use App\Models\Car;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentInventory extends TableWidget
{
    protected static ?string $heading = 'Recently updated inventory';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Car::query()->with(['make', 'carModel', 'carStand'])->latest('updated_at')->limit(10))
            ->columns([
                TextColumn::make('stock_number')->label('Stock')->searchable(),
                TextColumn::make('display_name')->label('Vehicle'),
                TextColumn::make('status')->badge(),
                TextColumn::make('carStand.name')->label('Stand'),
                TextColumn::make('updated_at')->since(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                Action::make('edit')->url(fn (Car $record): string => CarResource::getUrl('edit', ['record' => $record]))->icon('heroicon-o-pencil-square'),
            ])->paginated(false)->emptyStateHeading('No recent inventory');
    }

    public static function canView(): bool
    {
        return auth()->user()?->isAdministrator() ?? false;
    }
}
