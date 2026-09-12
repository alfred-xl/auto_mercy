<?php

namespace App\Filament\Resources\Cars\Schemas;

use App\Actions\CarImages\DeleteCarImage;
use App\Actions\Cars\EnsureCarReadyForPublication;
use App\Enums\DrivetrainType;
use App\Enums\FuelType;
use App\Enums\ListingCategory;
use App\Enums\MileageUnit;
use App\Enums\TransmissionType;
use App\Models\Car;
use App\Models\Feature;
use App\Models\User;
use DomainException;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Throwable;

class CarForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Wizard::make(self::steps())
                ->persistStepInQueryString()
                ->skippable(false)
                ->nextAction(fn (Action $action): Action => $action->label('Continue')),
        ]);
    }

    /** @return list<Step> */
    public static function steps(): array
    {
        return [
            Step::make('Basic information')->schema([
                Section::make('Vehicle identity')->description('Stock number and public URL are generated automatically.')->columns(2)->schema([
                    Select::make('listing_category')->options(self::options(ListingCategory::cases()))->required(),
                    TextInput::make('make')->required()->maxLength(100)->datalist(['Toyota', 'Lexus', 'Honda', 'Mercedes-Benz', 'Ford', 'Hyundai', 'Nissan']),
                    TextInput::make('model')->required()->maxLength(100),
                    TextInput::make('trim')->maxLength(255),
                    TextInput::make('year')->numeric()->required()->minValue(1900)->maxValue(now()->year + 1),
                    TextInput::make('body_type')->label('Body type')->maxLength(100)->datalist(['Sedan', 'SUV', 'Hatchback', 'Pickup', 'Coupe', 'Van']),
                ]),
            ]),
            Step::make('Pricing')->schema([
                Section::make('Pricing')->columns(2)->schema([
                    TextInput::make('price_amount')->label('Current price')->numeric()->prefix('₦')->minValue(1)->required(),
                    TextInput::make('previous_price_amount')->label('Previous price')->numeric()->prefix('₦')->minValue(1)->gt('price_amount')->helperText('Only enter this when the vehicle has a genuine discount.'),
                ]),
            ]),
            Step::make('Specifications')->schema([
                Section::make('Optional specifications')->description('These details can be completed later while the vehicle remains a draft.')->columns(3)->schema([
                    TextInput::make('mileage')->numeric()->minValue(0),
                    Select::make('mileage_unit')->options(self::options(MileageUnit::cases()))->default(MileageUnit::Kilometres->value),
                    Select::make('transmission')->options(self::options(TransmissionType::cases())),
                    Select::make('fuel_type')->options(self::options(FuelType::cases())),
                    Select::make('drivetrain')->options(self::options(DrivetrainType::cases())),
                    TextInput::make('engine')->maxLength(255),
                    TextInput::make('exterior_colour')->label('Exterior colour')->maxLength(255),
                    TextInput::make('interior_colour')->label('Interior colour')->maxLength(255),
                ]),
            ]),
            Step::make('Features')->schema([
                Section::make('Vehicle features')->schema([
                    Select::make('features')
                        ->label('Features')
                        ->multiple()
                        ->relationship('features', 'name', fn (Builder $query): Builder => $query->orderBy('name'))
                        ->searchable(['name'])
                        ->preload()
                        ->placeholder('Search and select vehicle features')
                        ->helperText('Select existing features or create a new reusable feature.')
                        ->createOptionForm([
                            TextInput::make('name')
                                ->required()
                                ->maxLength(120),
                        ])
                        ->createOptionUsing(function (array $data): int {
                            $name = Str::squish($data['name']);
                            $feature = Feature::query()->firstOrCreate(
                                ['normalized_name' => Feature::normalizeName($name)],
                                ['name' => $name],
                            );

                            return $feature->getKey();
                        }),
                ]),
            ]),
            Step::make('Description')->schema([
                Section::make('Customer-facing description')->schema([
                    RichEditor::make('description')->required()->columnSpanFull(),
                ]),
            ]),
            Step::make('Photos')->schema([
                Section::make('Photo gallery')
                    ->description('Review saved photos, remove any you no longer need, then add one or several new photos.')
                    ->schema([
                        View::make('filament.resources.cars.components.edit-gallery')
                            ->key('saved_vehicle_photos')
                            ->registerActions([
                                self::removeSavedPhotoAction(),
                            ]),
                        FileUpload::make('gallery_files')
                            ->label('Upload new photos')
                            ->helperText('Select or drag multiple photos. Previews appear here before you save the vehicle.')
                            ->multiple()
                            ->appendFiles()
                            ->reorderable()
                            ->maxParallelUploads(2)
                            ->panelLayout('grid')
                            ->imagePreviewHeight('128')
                            ->image()
                            ->imageResizeMode('contain')
                            ->imageResizeTargetWidth('2400')
                            ->imageResizeTargetHeight('1800')
                            ->imageResizeUpscale(false)
                            ->storeFiles(false)
                            ->previewable()
                            ->openable()
                            ->acceptedFileTypes((array) config('automercy.media.image_mime_types'))
                            ->maxSize((int) config('automercy.media.image_max_kilobytes'))
                            ->maxFiles(20),
                    ]),
            ]),
            Step::make('Status')->schema([
                Section::make('Publication')->description('Vehicles are always saved as drafts first. Publish them from the vehicle page after the readiness check passes.')->columns(2)->schema([
                    Toggle::make('is_featured')->label('Featured vehicle'),
                    Placeholder::make('current_status')->label('Current status')->content(fn (?Car $record): string => $record?->status?->label() ?? 'Draft'),
                    Placeholder::make('readiness')->label('Readiness')->content(function (?Car $record): string {
                        if (! $record?->exists) {
                            return 'Save the draft, then add at least one ready image before publishing.';
                        }

                        $missing = app(EnsureCarReadyForPublication::class)->missingRequirements($record);

                        return $missing === [] ? 'Ready to publish.' : 'Still required: '.implode(', ', $missing).'.';
                    })->columnSpanFull(),
                ]),
            ]),
        ];
    }

    private static function removeSavedPhotoAction(): Action
    {
        return Action::make('remove_saved_photo')
            ->label('Remove photo')
            ->icon('heroicon-o-trash')
            ->iconButton()
            ->color('danger')
            ->tooltip('Remove photo')
            ->requiresConfirmation()
            ->modalHeading('Remove this photo?')
            ->modalDescription('The photo and its generated image sizes will be permanently removed. This action cannot be undone.')
            ->modalSubmitActionLabel('Remove photo')
            ->authorize(fn (Car $record): bool => auth()->user()?->can('update', $record) ?? false)
            ->action(function (Car $record, array $arguments): void {
                try {
                    $image = $record->images()->findOrFail((int) ($arguments['image'] ?? 0));
                    $filename = basename($image->path);

                    app(DeleteCarImage::class)->execute($record, $image, self::actor());

                    $record
                        ->unsetRelation('images')
                        ->unsetRelation('coverImage')
                        ->load(['images', 'coverImage']);

                    Notification::make()
                        ->title('Photo removed')
                        ->body($filename.' was removed from the vehicle gallery.')
                        ->success()
                        ->send();
                } catch (Throwable $exception) {
                    if (! $exception instanceof DomainException) {
                        report($exception);
                    }

                    Notification::make()
                        ->title('Photo could not be removed')
                        ->body($exception instanceof DomainException
                            ? $exception->getMessage()
                            : 'An unexpected error occurred while removing the photo. Please try again.')
                        ->danger()
                        ->send();
                }
            });
    }

    private static function actor(): User
    {
        /** @var User $user */
        $user = auth()->user();

        return $user;
    }

    /** @param array<int, object> $cases */
    private static function options(array $cases): array
    {
        return collect($cases)->mapWithKeys(fn ($case): array => [$case->value => $case->label()])->all();
    }
}
