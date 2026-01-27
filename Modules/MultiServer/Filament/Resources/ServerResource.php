<?php

namespace Modules\MultiServer\Filament\Resources;

use Modules\MultiServer\Filament\Resources\ServerResource\Pages;
use Modules\MultiServer\Models\Server;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Services\XUIService;
use Filament\Notifications\Notification;

class ServerResource extends Resource
{
    protected static ?string $model = Server::class;
    protected static ?string $navigationIcon = 'heroicon-o-server';
    protected static ?string $navigationGroup = 'مولتی سرور';
    protected static ?string $label = 'سرور';
    protected static ?string $pluralLabel = 'سرورها';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('اطلاعات اتصال پنل')
                    ->description('اطلاعات ورود به پنل سنایی/X-UI سرور مقصد را وارد کنید.')
                    ->schema([
                        Forms\Components\Select::make('location_id')
                            ->relationship('location', 'name')
                            ->label('لوکیشن (کشور)')
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')->required()->label('نام کشور'),
                                Forms\Components\TextInput::make('slug')->required()->label('شناسه'),
                                Forms\Components\TextInput::make('flag')->label('پرچم'),
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('name')
                            ->label('نام سرور')
                            ->required()
                            ->placeholder('مثال: Server Germany 1'),

                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('ip_address')
                                ->label('آدرس IP یا دامنه')
                                ->required()
                                ->placeholder('مثال: sub.domain.com (بدون http/https)'),

                            Forms\Components\TextInput::make('port')
                                ->label('پورت پنل')
                                ->numeric()
                                ->required()
                                ->default(54321),
                        ]),

                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('username')
                                ->label('نام کاربری پنل')
                                ->required(),

                            Forms\Components\TextInput::make('password')
                                ->label('رمز عبور پنل')
                                ->password()
                                ->revealable()
                                ->required(),
                        ]),

                        Forms\Components\TextInput::make('path')
                            ->label('URL Path')
                            ->default('/')
                            ->placeholder('/')
                            ->helperText('اگر پنل روی ساب‌فولدر است (مثلاً /panel/) وارد کنید.'),

                        Forms\Components\Toggle::make('is_https')
                            ->label('اتصال امن (SSL/HTTPS)')
                            ->default(false)
                            ->inline(false),

                        // ====================================================
                        // 🚀 انتخاب هوشمند اینباند (روش جدید و تضمینی)
                        // ====================================================
                        Forms\Components\TextInput::make('inbound_id')
                            ->label('شناسه اینباند (Inbound ID)')
                            ->required()
                            ->numeric()
                            ->helperText('برای دریافت لیست، دکمه سمت چپ را بزنید.')
                            ->suffixAction(
                                Forms\Components\Actions\Action::make('selectInbound')
                                    ->icon('heroicon-o-list-bullet')
                                    ->label('انتخاب از لیست')
                                    ->color('primary')
                                    ->modalHeading('لیست اینباندهای موجود در سرور')
                                    ->modalSubmitActionLabel('تایید و انتخاب') // دکمه تایید اضافه شد
                                    ->form(function (Forms\Get $get) {
                                        // 1. تمیزکاری آدرس (حذف http/https)
                                        $rawIp = $get('ip_address');
                                        $cleanIp = str_replace(['http://', 'https://', '/'], '', $rawIp);

                                        // 2. ساخت آدرس اتصال
                                        $protocol = $get('is_https') ? 'https' : 'http';
                                        $port = $get('port');
                                        $path = $get('path');

                                        // اطمینان از فرمت درست آدرس
                                        $host = "{$protocol}://{$cleanIp}:{$port}{$path}";

                                        $user = $get('username');
                                        $pass = $get('password');

                                        if (!$user || !$pass || !$cleanIp) {
                                            return [
                                                Forms\Components\Placeholder::make('error')
                                                    ->content('❌ لطفاً ابتدا فیلدهای آدرس، پورت، نام کاربری و رمز عبور را پر کنید.')
                                                    ->extraAttributes(['class' => 'text-danger-600'])
                                            ];
                                        }

                                        try {
                                            // 3. اتصال به سرور
                                            $xui = new \App\Services\XUIService($host, $user, $pass);
                                            if (!$xui->login()) {
                                                throw new \Exception('اتصال به پنل ناموفق بود. نام کاربری یا رمز عبور اشتباه است.');
                                            }

                                            $inbounds = $xui->getInbounds();
                                            if (empty($inbounds)) {
                                                throw new \Exception('هیچ اینباندی در این سرور یافت نشد.');
                                            }

                                            // 4. آماده‌سازی گزینه‌ها برای نمایش
                                            $options = [];
                                            foreach ($inbounds as $inbound) {
                                                $id = $inbound['id'];
                                                $remark = $inbound['remark'] ?? 'بدون نام';
                                                $protocol = strtoupper($inbound['protocol'] ?? 'UNKNOWN');
                                                $port = $inbound['port'] ?? '?';

                                                // نمایش اطلاعات کامل در لیست
                                                $options[$id] = "ID: {$id}  |  {$remark}  |  {$protocol} : {$port}";
                                            }

                                            return [
                                                Forms\Components\Radio::make('selected_inbound')
                                                    ->label('یکی از اینباندها را انتخاب کنید:')
                                                    ->options($options)
                                                    ->required()
                                                    ->columns(1) // نمایش خطی و مرتب
                                            ];

                                        } catch (\Exception $e) {
                                            return [
                                                Forms\Components\Placeholder::make('error')
                                                    ->content('خطا در دریافت لیست: ' . $e->getMessage())
                                                    ->extraAttributes(['class' => 'text-danger-600 bg-danger-50 p-3 rounded'])
                                            ];
                                        }
                                    })
                                    ->action(function (array $data, Forms\Set $set) {
                                        // 5. قرار دادن مقدار انتخاب شده در فیلد اصلی
                                        if (isset($data['selected_inbound'])) {
                                            $set('inbound_id', $data['selected_inbound']);
                                            Notification::make()->title('اینباند انتخاب شد')->success()->send();
                                        }
                                    })
                            ),
                        // ====================================================

                        Forms\Components\Toggle::make('is_active')
                            ->label('سرور فعال است')
                            ->default(true)
                            ->inline(false),
                    ]),

                Forms\Components\Section::make('مدیریت ظرفیت')->schema([
                    Forms\Components\TextInput::make('capacity')
                        ->numeric()
                        ->default(100)
                        ->label('ظرفیت کل')
                        ->helperText('حداکثر تعداد کاربر مجاز'),

                    Forms\Components\TextInput::make('current_users')
                        ->numeric()
                        ->default(0)
                        ->label('کاربران فعلی')
                        ->disabled(),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('نام سرور')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('location.name')
                    ->label('لوکیشن')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('ip_address')
                    ->label('آدرس IP')
                    ->copyable(),

                Tables\Columns\TextColumn::make('current_users')
                    ->label('وضعیت ظرفیت')
                    ->formatStateUsing(fn ($record) => "{$record->current_users} / {$record->capacity}")
                    ->color(fn ($record) => $record->current_users >= $record->capacity ? 'danger' : 'success')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('وضعیت')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServers::route('/'),
            'create' => Pages\CreateServer::route('/create'),
            'edit' => Pages\EditServer::route('/{record}/edit'),
        ];
    }
}
