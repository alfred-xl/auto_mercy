<?php

namespace App\Filament\Support;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ReferenceResourceDefinition
{
    public static function simpleInfolist(Schema $schema, bool $category = false, bool $withMake = false): Schema
    {
        return $schema->components([
            Section::make('Details')->columns(2)->schema([
                ...($withMake ? [TextEntry::make('make.name')->label('Make')] : []),
                TextEntry::make('name'), TextEntry::make('slug'),
                ...($category ? [TextEntry::make('category')] : []),
                IconEntry::make('is_active')->boolean(), TextEntry::make('sort_order')->label('Order'), TextEntry::make('cars_count')->state(fn (Model $record): int => $record->cars()->count())->label('Cars'),
                TextEntry::make('updated_at')->dateTime(),
            ]),
        ]);
    }

    public static function standInfolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Location and contact')->columns(2)->schema([
                TextEntry::make('name'), TextEntry::make('slug'), TextEntry::make('address')->columnSpanFull(), TextEntry::make('city'), TextEntry::make('state'),
                TextEntry::make('phone'), TextEntry::make('whatsapp'), TextEntry::make('email'), TextEntry::make('map_url')->url(fn (Model $record): ?string => $record->map_url)->openUrlInNewTab(),
                TextEntry::make('opening_hours')->listWithLineBreaks(), IconEntry::make('is_active')->boolean(),
            ]),
        ]);
    }

    public static function simpleForm(Schema $schema, bool $category = false): Schema
    {
        return $schema->components([
            Section::make('Details')->columns(2)->schema([
                TextInput::make('name')->required()->maxLength(255)->live(onBlur: true)->afterStateUpdated(fn (?string $state, $set) => $set('slug', Str::slug((string) $state))),
                TextInput::make('slug')->required()->maxLength(255)->unique(ignoreRecord: true),
                ...($category ? [TextInput::make('category')->required()->maxLength(100)] : []),
                TextInput::make('sort_order')->numeric()->minValue(0)->default(0)->required(),
                Toggle::make('is_active')->default(true),
            ]),
        ]);
    }

    public static function modelForm(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Model')->columns(2)->schema([
                Select::make('make_id')->relationship('make', 'name')->searchable()->preload()->required(),
                TextInput::make('name')->required()->maxLength(255)->live(onBlur: true)->afterStateUpdated(fn (?string $state, $set) => $set('slug', Str::slug((string) $state))),
                TextInput::make('slug')->required()->maxLength(255),
                TextInput::make('sort_order')->numeric()->minValue(0)->default(0)->required(),
                Toggle::make('is_active')->default(true),
            ]),
        ]);
    }

    public static function standForm(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Location')->columns(2)->schema([
                TextInput::make('name')->required()->maxLength(255)->live(onBlur: true)->afterStateUpdated(fn (?string $state, $set) => $set('slug', Str::slug((string) $state))),
                TextInput::make('slug')->required()->maxLength(255)->unique(ignoreRecord: true),
                Textarea::make('address')->required()->columnSpanFull(),
                TextInput::make('city')->required(), TextInput::make('state')->required(),
                TextInput::make('latitude')->numeric(), TextInput::make('longitude')->numeric(),
                TextInput::make('map_url')->label('Map URL')->url()->columnSpanFull(),
            ]),
            Section::make('Contact and hours')->columns(2)->schema([
                TextInput::make('phone')->tel()->required(), TextInput::make('whatsapp')->tel(), TextInput::make('email')->email(),
                KeyValue::make('opening_hours')->keyLabel('Day')->valueLabel('Opening hours')->columnSpanFull(),
                TextInput::make('sort_order')->numeric()->minValue(0)->default(0)->required(), Toggle::make('is_active')->default(true),
            ]),
        ]);
    }

    public static function simpleTable(Table $table, bool $category = false, bool $withMake = false): Table
    {
        return $table->defaultSort('sort_order')->columns([
            ...($withMake ? [TextColumn::make('make.name')->label('Make')->searchable()->sortable()] : []),
            TextColumn::make('name')->searchable()->sortable(),
            ...($category ? [TextColumn::make('category')->searchable()->sortable()] : []),
            TextColumn::make('cars_count')->counts('cars')->label('Cars')->sortable(),
            IconColumn::make('is_active')->boolean(),
            TextColumn::make('sort_order')->label('Order')->sortable(),
        ])->filters([TernaryFilter::make('is_active')->label('Active')])
            ->recordActions([
                ViewAction::make(), EditAction::make(),
                DeleteAction::make()->visible(fn (Model $record): bool => $record->cars()->doesntExist()),
            ]);
    }

    public static function standTable(Table $table): Table
    {
        return $table->defaultSort('sort_order')->columns([
            TextColumn::make('name')->searchable()->sortable(), TextColumn::make('city')->searchable(), TextColumn::make('state')->searchable(),
            TextColumn::make('phone'), TextColumn::make('cars_count')->counts('cars')->label('Cars'), IconColumn::make('is_active')->boolean(),
        ])->filters([TernaryFilter::make('is_active')->label('Active')])
            ->recordActions([ViewAction::make(), EditAction::make(), DeleteAction::make()->visible(fn (Model $record): bool => $record->cars()->doesntExist())]);
    }
}
