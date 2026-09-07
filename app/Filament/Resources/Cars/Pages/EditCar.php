<?php

namespace App\Filament\Resources\Cars\Pages;

use App\Actions\CarImages\UploadCarFormImages;
use App\Filament\Resources\Cars\Actions\CarLifecycleActions;
use App\Filament\Resources\Cars\CarResource;
use App\Filament\Resources\Cars\Schemas\CarForm;
use App\Models\User;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Concerns\HasWizard;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Wizard\Step;

class EditCar extends EditRecord
{
    use HasWizard;

    protected static string $resource = CarResource::class;

    private array $pendingGalleryFiles = [];

    /** @return list<Step> */
    public function getSteps(): array
    {
        return CarForm::steps();
    }

    protected function getHeaderActions(): array
    {
        $actions = collect(CarLifecycleActions::make())->keyBy(fn ($action): string => $action->getName());
        $statusActions = collect(['publish', 'mark_available', 'mark_sold'])
            ->map(fn (string $name) => $actions->pull($name))
            ->filter()
            ->values()
            ->all();

        return [
            ViewAction::make()->label('View vehicle')->icon('heroicon-o-eye'),
            ...$statusActions,
            ActionGroup::make($actions->values()->all())
                ->label('More actions')
                ->icon('heroicon-m-ellipsis-vertical')
                ->button()
                ->color('gray'),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->pendingGalleryFiles = $data['gallery_files'] ?? [];
        unset($data['gallery_files']);

        return $data;
    }

    protected function afterSave(): void
    {
        $uploaded = app(UploadCarFormImages::class)->execute($this->record, $this->pendingGalleryFiles, [], null, $this->actor());
        $this->data['gallery_files'] = [];

        if ($uploaded->isNotEmpty()) {
            $this->record
                ->unsetRelation('images')
                ->unsetRelation('coverImage')
                ->load(['images', 'coverImage']);

            Notification::make()->title($uploaded->count().' vehicle photo(s) prepared')->success()->send();
        }
    }

    private function actor(): User
    {
        /** @var User $user */
        $user = auth()->user();

        return $user;
    }
}
