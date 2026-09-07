<?php

namespace App\Filament\Resources\Leads\Tables;

use App\Enums\LeadStatus;
use App\Filament\Resources\Leads\Schemas\LeadForm;
use App\Models\Lead;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LeadsTable
{
    public static function configure(Table $table): Table
    {
        return $table->defaultSort('created_at', 'desc')->columns([
            TextColumn::make('customer_name')->label('Customer')->description(fn (Lead $record): string => $record->phone)->searchable(['customer_name', 'phone', 'email'])->weight('semibold'),
            TextColumn::make('car.display_name')->label('Vehicle')->placeholder('General enquiry')->wrap(),
            TextColumn::make('status')->badge()->formatStateUsing(fn (LeadStatus $state): string => $state->label())->color(fn (LeadStatus $state): string => $state->color()),
            TextColumn::make('follow_up_at')->label('Follow-up')->dateTime('d M Y, H:i')->placeholder('—')->sortable(),
            TextColumn::make('inspection_at')->label('Inspection')->dateTime('d M Y, H:i')->placeholder('—')->sortable(),
            TextColumn::make('created_at')->label('Received')->since()->sortable(),
        ])->filters([
            SelectFilter::make('status')->options(LeadForm::statusOptions()),
            SelectFilter::make('car_id')->label('Vehicle')->relationship('car', 'stock_number')->searchable()->preload(),
            Filter::make('overdue_follow_up')->query(fn (Builder $query): Builder => $query->whereNotNull('follow_up_at')->where('follow_up_at', '<', now())->whereNotIn('status', [LeadStatus::Won, LeadStatus::Lost])),
            Filter::make('upcoming_inspection')->query(fn (Builder $query): Builder => $query->whereBetween('inspection_at', [now(), now()->addDays(7)])),
        ])->recordActions([
            ActionGroup::make([
                ViewAction::make(), EditAction::make(),
                Action::make('change_status')->schema([Select::make('status')->options(LeadForm::statusOptions())->required()])->fillForm(fn (Lead $record): array => ['status' => $record->status->value])->action(fn (Lead $record, array $data) => $record->update(['status' => $data['status']])),
                Action::make('call')->icon('heroicon-o-phone')->url(fn (Lead $record): string => 'tel:'.$record->phone),
                Action::make('whatsapp')->icon('heroicon-o-chat-bubble-left-right')->color('success')->url(fn (Lead $record): string => self::whatsappUrl($record))->openUrlInNewTab(),
            ])->icon('heroicon-m-ellipsis-vertical')->iconButton()->tooltip('Lead actions'),
        ]);
    }

    private static function whatsappUrl(Lead $lead): string
    {
        $phone = preg_replace('/\D+/', '', $lead->phone) ?? '';
        $phone = str_starts_with($phone, '0') ? '234'.substr($phone, 1) : $phone;

        return 'https://wa.me/'.$phone.'?text='.rawurlencode("Hello {$lead->customer_name}, this is Auto Mercy following up on your enquiry.");
    }
}
