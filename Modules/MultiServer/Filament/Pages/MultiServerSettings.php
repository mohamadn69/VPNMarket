<?php

namespace Modules\MultiServer\Filament\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class MultiServerSettings extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static ?string $navigationGroup = 'مولتی سرور';
    protected static ?string $navigationLabel = 'تنظیمات مولتی سرور';
    protected static ?string $title = 'تنظیمات سیستم مولتی سرور';
    protected static string $view = 'multiserver::filament.pages.settings';



    public ?array $data = [];

    public function mount(): void
    {
        $settings = Setting::all()->pluck('value', 'key')->toArray();
        $this->form->fill($settings);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('نمایش در ربات')->schema([

                    Forms\Components\Toggle::make('ms_show_capacity')
                        ->label('نمایش تعداد ظرفیت باقی‌مانده')
                        ->helperText('اگر فعال باشد، کنار اسم کشور تعداد ظرفیت خالی نمایش داده می‌شود. (مثال: 🇩🇪 آلمان (50 عدد))')
                        ->default(true),

                    Forms\Components\Toggle::make('ms_hide_full_locations')
                        ->label('مخفی کردن لوکیشن‌های پر شده')
                        ->helperText('اگر فعال باشد، وقتی ظرفیت تمام سرورهای یک کشور پر شد، دکمه آن کشور از ربات حذف می‌شود.')
                        ->live()
                        ->default(false),

                    Forms\Components\Textarea::make('ms_full_location_message')
                        ->label('پیام تکمیل ظرفیت')
                        ->helperText('اگر تیک بالا خاموش باشد و کاربر روی کشور پر شده کلیک کند، این پیام نمایش داده می‌شود.')
                        ->default("❌ ظرفیت این لوکیشن فعلاً تکمیل است.\n⏰ لطفاً ۵ ساعت دیگر مجددا تلاش کنید.")
                        ->rows(3)
                        ->hidden(fn (Forms\Get $get) => $get('ms_hide_full_locations') === true),
                ]),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
            Cache::forget("setting.{$key}");
        }


        Cache::forget('settings');

        Notification::make()
            ->title('تنظیمات ذخیره شد')
            ->success()
            ->send();
    }


    public function getHeading(): string
    {
        return 'تنظیمات مولتی سرور';
    }
}
