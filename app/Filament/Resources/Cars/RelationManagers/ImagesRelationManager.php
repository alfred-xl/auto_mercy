<?php

namespace App\Filament\Resources\Cars\RelationManagers;

use App\Actions\CarImages\DeleteCarImage;
use App\Actions\CarImages\ReorderCarImages;
use App\Actions\CarImages\UploadCarImages;
use App\Models\CarImage;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ImagesRelationManager extends RelationManager
{
    protected static string $relationship = 'images';

    protected static ?string $title = 'Gallery';

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                ImageColumn::make('path')->label('Image')->state(fn (CarImage $record): string => $record->variantPath('thumbnail'))->disk((string) config('automercy.media.disk'))->square()->size(56),
                TextColumn::make('file')->state(fn (CarImage $record): string => basename($record->path))->wrap(),
                TextColumn::make('alt_text')->label('Alternative text')->placeholder('Generated automatically')->wrap(),
                TextColumn::make('dimensions_size')->label('Dimensions / Size')->state(function (CarImage $record): string {
                    $width = data_get($record->derivatives, 'large.webp.width');
                    $height = data_get($record->derivatives, 'large.webp.height');
                    $bytes = data_get($record->derivatives, 'large.webp.file_size_bytes');
                    $dimensions = $width && $height ? "{$width} × {$height}" : '—';
                    $size = $bytes ? number_format($bytes / 1024, 1).' KB' : '—';

                    return "{$dimensions} · {$size}";
                }),
                IconColumn::make('cover')->label('Primary')->state(fn (CarImage $record): bool => $this->getOwnerRecord()->coverImage?->is($record) ?? false)->icon(fn (bool $state): string => $state ? 'heroicon-s-check-circle' : 'heroicon-o-check-circle')->color(fn (bool $state): string => $state ? 'success' : 'gray')->action(fn (CarImage $record) => $this->setCover($record)),
                TextColumn::make('sort_order')->label('Order')->sortable(),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()->schema([TextInput::make('alt_text')->label('Alternative text')->maxLength(255)]),
                    Action::make('replace')->icon('heroicon-o-arrow-path')->schema([
                        FileUpload::make('replacement')->image()->required()->storeFiles(false)->acceptedFileTypes((array) config('automercy.media.image_mime_types'))->maxSize((int) config('automercy.media.image_max_kilobytes')),
                    ])->action(function (CarImage $record, array $data): void {
                        $file = $data['replacement'] ?? null;

                        if (! $file instanceof TemporaryUploadedFile) {
                            return;
                        }

                        $position = $record->sort_order;
                        $replacement = app(UploadCarImages::class)->execute($this->getOwnerRecord(), [$file], $this->actor())->firstOrFail();
                        $replacement->update(['alt_text' => $record->alt_text]);
                        app(DeleteCarImage::class)->execute($this->getOwnerRecord(), $record, $this->actor());
                        $ids = $this->getOwnerRecord()->images()->pluck('id')->map(fn (int $id): int => $id)->reject(fn (int $id): bool => $id === $replacement->getKey())->values()->all();
                        array_splice($ids, min($position, count($ids)), 0, [$replacement->getKey()]);
                        app(ReorderCarImages::class)->execute($this->getOwnerRecord(), $ids);
                    }),
                    Action::make('move_up')->icon('heroicon-o-arrow-up')->visible(fn (CarImage $record): bool => $record->sort_order > 0)->action(fn (CarImage $record) => $this->move($record, -1)),
                    Action::make('move_down')->icon('heroicon-o-arrow-down')->visible(fn (CarImage $record): bool => $record->sort_order < $this->getOwnerRecord()->images()->count() - 1)->action(fn (CarImage $record) => $this->move($record, 1)),
                    Action::make('delete')->color('danger')->icon('heroicon-o-trash')->requiresConfirmation()->action(fn (CarImage $record) => app(DeleteCarImage::class)->execute($this->getOwnerRecord(), $record, $this->actor())),
                ])->icon('heroicon-m-ellipsis-vertical')->iconButton()->tooltip('Image actions'),
            ]);
    }

    private function setCover(CarImage $image): void
    {
        if ($image->processing_status->value !== 'ready') {
            return;
        }

        $ids = $this->getOwnerRecord()->images()->pluck('id')->map(fn (int $id): int => $id)->all();
        $ids = [$image->getKey(), ...array_values(array_filter($ids, fn (int $id): bool => $id !== $image->getKey()))];
        app(ReorderCarImages::class)->execute($this->getOwnerRecord(), $ids);
    }

    private function move(CarImage $image, int $direction): void
    {
        $ids = $this->getOwnerRecord()->images()->pluck('id')->map(fn (int $id): int => $id)->all();
        $index = array_search($image->getKey(), $ids, true);
        $target = $index + $direction;

        if ($index === false || ! isset($ids[$target])) {
            return;
        }

        [$ids[$index], $ids[$target]] = [$ids[$target], $ids[$index]];
        app(ReorderCarImages::class)->execute($this->getOwnerRecord(), $ids);
    }

    private function actor(): User
    {
        /** @var User $user */
        $user = auth()->user();

        return $user;
    }
}
