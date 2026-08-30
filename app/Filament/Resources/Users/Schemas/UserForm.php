<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\UserRole;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Administrator account')->columns(2)->schema([
                TextInput::make('name')->required()->maxLength(255),
                TextInput::make('email')->email()->required()->maxLength(255)->unique(ignoreRecord: true),
                Select::make('role')->options(collect(UserRole::cases())->mapWithKeys(fn (UserRole $role): array => [$role->value => $role->label()])->all())->required(),
                Toggle::make('is_active')->default(true),
            ]),
            Section::make('Password')->columns(2)->schema([
                TextInput::make('password')->password()->revealable()->minLength(12)->same('password_confirmation')
                    ->required(fn (string $operation): bool => $operation === 'create')->dehydrated(fn (?string $state): bool => filled($state)),
                TextInput::make('password_confirmation')->label('Confirm password')->password()->revealable()->dehydrated(false)
                    ->required(fn (string $operation): bool => $operation === 'create'),
            ]),
        ]);
    }
}
