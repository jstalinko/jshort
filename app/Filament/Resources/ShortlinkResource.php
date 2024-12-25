<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Shortlink;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use App\Services\IpApiService;
use Filament\Resources\Resource;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Actions\Action;
use App\Filament\Resources\ShortlinkResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ShortlinkResource\RelationManagers;
use Webbingbrasil\FilamentCopyActions\Tables\Actions\CopyAction;

class ShortlinkResource extends Resource
{
    protected static ?string $model = Shortlink::class;

    protected static ?string $navigationIcon = 'heroicon-o-link';
    protected static ?int $sort = 1;
    public static function getTableQuery(): Builder
    {
        $query = Shortlink::query();

        if (!auth()->user()->hasRole('super_admin')) {
            $query->where('user_id', auth()->id());
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('short')
                    ->required()
                    ->unique(Shortlink::class, 'short', ignoreRecord: true)
                    ->suffixAction(
                        Action::make('generate')
                            ->icon('heroicon-m-sparkles')
                            ->button()
                            ->color('primary')
                            ->label('Generate')
                            ->action(function (TextInput $component) {
                                do {
                                    // Generate a new short code
                                    $shortCode = Str::random(8); // or your preferred length

                                    // Check if it exists in the database
                                    $exists = Shortlink::where('short', $shortCode)->exists();
                                } while ($exists);

                                // Set the unique short code
                                $component->state($shortCode);
                            })
                            ->tooltip('Generate unique short link')
                    )
                    ->helperText('Short code must be unique')
                    ->maxLength(8),
                Forms\Components\Select::make('method')
                    ->required()
                    ->options([
                        'header' => 'PHP HEADER 302 REDIRECT',
                        'meta' => 'META HTML TAG REFRESH',
                        'js' => 'JAVASCRIPT WINDOW LOCATION'
                    ])->helperText('Tips: Jika url anda tidak ingin nampak referer , maka gunakanlah php header sebagai metode redirect'),

                Forms\Components\Section::make('General Setting')->schema([
                    Forms\Components\TextInput::make('real_url')
                        ->required()
                        ->helperText('Tips: URL Target, url yang ingin di proteksi'),
                    Forms\Components\TextInput::make('cloak_url')
                        ->required()
                        ->helperText('Tips: Manipulasi URL , pengalihan url jika tidak sesuai dengan rules.'),
                    Forms\Components\Select::make('lock_country')
                        ->required()
                        ->options(fn(IpApiService $ipapi) => $ipapi->country())
                        ->multiple()
                        ->native(false)
                        ->helperText('Tips: Kunci visitor deri negara tertentu,jika sudah all tidak perlu pilih yang lain'),
                    Forms\Components\Select::make('lock_browser')
                        ->required()
                        ->options([
                            'all' => 'Allow All browser',
                            'fb_browser' => 'FB Browser Only',
                            'chrome_browser' => 'Chrome Browser Only',
                            'fb_chrome' => 'Facebook & Chrome Support',
                            'opera_browser' => 'Opera Only'
                        ])
                        ->helperText('Tips: Kunci visitor dari browser tertentu'),
                    Forms\Components\Select::make('lock_device')
                        ->required()
                        ->options([
                            'all' => 'Allow All device',
                            'mobile' => 'Mobile',
                            'tablet' => 'Tablet',
                            'desktop' => 'Desktop'
                        ])
                        ->multiple()
                        ->native(false)
                        ->helperText('Tips: Kunci visitor dari device tertentu mobile or desktop or tablet or all, jika sudah all tidak perlu pilih yang lain.'),
                    Forms\Components\Select::make('lock_os')
                        ->required()
                        ->options([
                            'all' => 'Allow All OS',
                            'windows' => 'Windows',
                            'linux' => 'Linux',
                            'macos' => 'MacOS',
                            'android' => 'Android',
                            'ios' => 'iOS',
                        ])
                        ->multiple()
                        ->native(false)
                        ->helperText('Tips: Kunci visitor dari Operating Systen tertentu,jika sudah all tidak perlu pilih yang lain.'),
                    Forms\Components\Select::make('lock_referer')
                        ->required()
                        ->options([
                            'all' =>  'Allow all referer',
                            'facebook' => 'Facebook',
                            'google' => 'Google',
                            'tiktok' => 'Tiktok',
                        ])->multiple()->native(false)
                        ->helperText('Tips: Kunci visitor dari referer atau sumber trafik tertentu'),

                    Forms\Components\TextInput::make('throttle')
                        ->required()
                        ->numeric()
                        ->default(10)
                        ->helperText('Tips: Throttle adalah maksimum User visit, jika sudah batas maksimum akan di redirect ke cloaking url'),
                ])->columns(2),

                Forms\Components\Section::make('Additional Blocking List')->schema([
                    Forms\Components\Textarea::make('block_isp')->rows(10)->helperText('Tips: Tambahkan ISP yang ingin anda blacklist dari traffic anda'),
                    Forms\Components\Textarea::make('block_ip')->rows(10)->helperText('Tips: Tambahkan IP yang ingin anda blacklist dari traffic anda')
                ])->columns(2),

                Forms\Components\Section::make('Optional Security')->schema([
                    Forms\Components\Toggle::make('block_vpn')
                        ->required()
                        ->helperText('Tips: Aktifkan untuk proteksi dari visitor yang menggunakan VPN atau PROXY'),
                    Forms\Components\Toggle::make('block_crawler')
                        ->label('Auto Block IE,ID,SG Country')
                        ->required()->helperText('Tips: Aktifkan untuk proteksi dari negara ID,IE,SG'),
                    Forms\Components\Toggle::make('logs')
                        ->required()->default(true)->label('Logs traffic')->helperText('Tips: Aktifkan untuk memantau traffic details'),
                    Forms\Components\Toggle::make('active')->helperText('* Aktif atau tidak nya shortlink')
                        ->required()->default(true),
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('short')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('real_url')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cloak_url')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_allowed')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_blocked')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('logs')
                    ->boolean(),
                Tables\Columns\IconColumn::make('active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                CopyAction::make('Copy Link')->icon('heroicon-o-link')->copyable(fn(Shortlink $short) => url('/'.$short->short))->color('danger'),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('Stats')->icon('heroicon-s-chart-pie')->color('success')
                    ->url(fn($record): string => static::getUrl('stat', ['record' => $record->id])),


            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListShortlinks::route('/'),
            'create' => Pages\CreateShortlink::route('/create'),
            'view' => Pages\ViewShortlink::route('/{record}'),
            'edit' => Pages\EditShortlink::route('/{record}/edit'),
            'stat' => Pages\StatShortlink::route('{record}/stat')
        ];
    }
}
