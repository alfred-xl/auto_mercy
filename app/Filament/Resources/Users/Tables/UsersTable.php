<?php

namespace App\Filament\Resources\Users\Tables;

use App\Enums\UserRole;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table->defaultSort('name')->columns([
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('email')->searchable()->copyable(),
            TextColumn::make('role')->badge()->formatStateUsing(fn (UserRole $state): string => $state->label()),
            IconColumn::make('is_active')->label('Active')->boolean(),
            TextColumn::make('last_login_at')->dateTime()->placeholder('Never')->sortable(),
            TextColumn::make('updated_at')->since()->sortable(),
        ])->filters([
            SelectFilter::make('role')->options(collect(UserRole::cases())->mapWithKeys(fn (UserRole $role): array => [$role->value => $role->label()])->all()),
        ])->recordActions([ViewAction::make(), EditAction::make()]);
    }
}
