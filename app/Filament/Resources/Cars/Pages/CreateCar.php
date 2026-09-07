<?php

namespace App\Filament\Resources\Cars\Pages;

use App\Actions\CarImages\UploadCarFormImages;
use App\Filament\Resources\Cars\CarResource;
use App\Filament\Resources\Cars\Schemas\CarForm;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Concerns\HasWizard;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Components\Wizard\Step;

class CreateCar extends CreateRecord
{
    use HasWizard;

    protected static string $resource = CarResource::class;

    private array $pendingGalleryFiles = [];

    /** @return list<Step> */
    public function getSteps(): array
    {
        return CarForm::steps();
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->pendingGalleryFiles = $data['gallery_files'] ?? [];
        unset($data['gallery_files']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $uploaded = app(UploadCarFormImages::class)->execute($this->record, $this->pendingGalleryFiles, [], null, $this->actor());

        if ($uploaded->isNotEmpty()) {
            Notification::make()->title($uploaded->count().' vehicle photo(s) prepared')->success()->send();
        }
    }

    protected function getRedirectUrl(): string
    {
        return CarResource::getUrl('view', ['record' => $this->record]);
    }

    private function actor(): User
    {
        /** @var User $user */
        $user = auth()->user();

        return $user;
    }
}
