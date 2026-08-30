<?php

namespace App\Filament\Resources\Cars\RelationManagers;

use App\Actions\CarImages\DeleteCarImage;
use App\Actions\CarImages\ReorderCarImages;
use App\Actions\CarImages\SetPrimaryCarImage;
use App\Actions\CarImages\UploadCarImages;
use App\Models\CarImage;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Gate;

class ImagesRelationManager extends RelationManager
{
    protected static string $relationship = 'images';

    protected static ?string $title = 'Gallery';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('alt_text')->label('Alternative text')->helperText('Describe what is visible for people using screen readers.')->required()->maxLength(255),
            Textarea::make('caption')->rows(3),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('display_order')
            ->recordTitleAttribute('original_filename')
            ->columns([
                ImageColumn::make('path')->label('Image')->disk(fn (CarImage $record): string => $record->disk)->square(),
                TextColumn::make('original_filename')->label('File')->wrap(),
                TextColumn::make('alt_text')->label('Alternative text')->placeholder('Needs review')->wrap(),
                TextColumn::make('dimensions')->state(fn (CarImage $record): string => "{$record->width} × {$record->height}"),
                IconColumn::make('primary')->state(fn (CarImage $record): bool => $this->getOwnerRecord()->primary_image_id === $record->getKey())->boolean(),
                TextColumn::make('display_order')->label('Order'),
            ])
            ->headerActions([
                Action::make('upload')
                    ->label('Upload images')->icon('heroicon-o-arrow-up-tray')
                    ->schema([
                        FileUpload::make('images')->multiple()->storeFiles(false)->image()
                            ->acceptedFileTypes((array) config('automercy.media.mime_types'))
                            ->maxSize((int) config('automercy.media.image_max_kilobytes'))->required(),
                    ])
                    ->action(function (array $data): void {
                        Gate::forUser($this->actor())->authorize('create', CarImage::class);
                        $uploaded = app(UploadCarImages::class)->execute($this->getOwnerRecord(), array_values($data['images']), $this->actor());
                        Notification::make()->title($uploaded->count().' image(s) uploaded')->body('Add accurate alternative text before publishing.')->success()->send();
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('primary')->label('Set primary')->icon('heroicon-o-star')
                    ->visible(fn (CarImage $record): bool => $this->getOwnerRecord()->primary_image_id !== $record->getKey())
                    ->action(function (CarImage $record): void {
                        Gate::forUser($this->actor())->authorize('update', $record);
                        app(SetPrimaryCarImage::class)->execute($this->getOwnerRecord(), $record, $this->actor());
                        Notification::make()->title('Primary image updated')->success()->send();
                    }),
                Action::make('move_up')->label('Move up')->icon('heroicon-o-arrow-up')
                    ->visible(fn (CarImage $record): bool => $record->display_order > 0)
                    ->action(fn (CarImage $record) => $this->move($record, -1)),
                Action::make('move_down')->label('Move down')->icon('heroicon-o-arrow-down')
                    ->visible(fn (CarImage $record): bool => $record->display_order < $this->getOwnerRecord()->images()->count() - 1)
                    ->action(fn (CarImage $record) => $this->move($record, 1)),
                Action::make('delete')->color('danger')->icon('heroicon-o-trash')->requiresConfirmation()
                    ->action(function (CarImage $record): void {
                        Gate::forUser($this->actor())->authorize('delete', $record);
                        $replacement = app(DeleteCarImage::class)->execute($this->getOwnerRecord(), $record, $this->actor());
                        Notification::make()->title('Image removed')->body($replacement ? 'The next gallery image is now primary.' : 'No primary image remains; publishing is blocked until one is selected.')->warning()->send();
                    }),
            ])
            ->emptyStateHeading('No gallery images')
            ->emptyStateDescription('Upload JPEG, PNG, or WebP images, then review their alternative text.');
    }

    private function move(CarImage $record, int $direction): void
    {
        Gate::forUser($this->actor())->authorize('update', $record);
        $ids = $this->getOwnerRecord()->images()->orderBy('display_order')->pluck('id')->map(fn (int $id): int => $id)->all();
        $index = array_search($record->getKey(), $ids, true);
        $target = $index + $direction;

        if ($index !== false && isset($ids[$target])) {
            [$ids[$index], $ids[$target]] = [$ids[$target], $ids[$index]];
            app(ReorderCarImages::class)->execute($this->getOwnerRecord(), $ids);
            Notification::make()->title('Gallery order updated')->success()->send();
        }
    }

    private function actor(): User
    {
        /** @var User $user */
        $user = auth()->user();

        return $user;
    }
}
