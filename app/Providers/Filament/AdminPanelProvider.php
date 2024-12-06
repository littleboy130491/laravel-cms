<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Jeffgreco13\FilamentBreezy\BreezyCore;
use Illuminate\Validation\Rules\Password;
use Filament\Tables\Columns\Column;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Gate;
use Filament\Navigation\NavigationGroup;
use ShuvroRoy\FilamentSpatieLaravelBackup\FilamentSpatieLaravelBackupPlugin;
use ShuvroRoy\FilamentSpatieLaravelHealth\FilamentSpatieLaravelHealthPlugin;
use pxlrbt\FilamentSpotlight\SpotlightPlugin;
use Filament\SpatieLaravelTranslatablePlugin;
class AdminPanelProvider extends PanelProvider
{

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->passwordReset() // Enable password reset
            ->colors([
                'primary' => Color::Amber,
            ])
            ->maxContentWidth(MaxWidth::Full)
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->resources([
                config('filament-logger.activity_resource')
            ])
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Contents')
                    ->icon('heroicon-o-document-text'),
                NavigationGroup::make()
                    ->label('Users')
                    ->icon('heroicon-o-users'),
                NavigationGroup::make()
                    ->label('Settings')
                    ->icon('heroicon-o-cog-6-tooth'),

            ])
            ->databaseNotifications()
            ->sidebarCollapsibleOnDesktop()
            ->unsavedChangesAlerts()
            ->databaseTransactions()
            //->spa()
            ->plugins([
                FilamentShieldPlugin::make(),
                BreezyCore::make()
                    ->myProfile(
                        shouldRegisterUserMenu: true, // Adds a user menu item for My Profile
                        shouldRegisterNavigation: true, // Show in main navigation
                        navigationGroup: 'Users', // Group name if navigation is enabled
                        hasAvatars: false, // Enable avatar upload
                        slug: 'my-profile' // Profile page URL
                    )
                    // Optional: Configure password update validation
                    ->passwordUpdateRules(
                        rules: [Password::default()->mixedCase()->uncompromised(3)],
                        requiresCurrentPassword: false,
                    ),
                \Awcodes\Curator\CuratorPlugin::make(),
                FilamentSpatieLaravelBackupPlugin::make()
                    ->usingPage(\App\Filament\Pages\Backups::class),
                FilamentSpatieLaravelHealthPlugin::make()
                    ->usingPage(\App\Filament\Pages\HealthCheckResults::class),
                SpotlightPlugin::make(),
                SpatieLaravelTranslatablePlugin::make()
                    ->defaultLocales(['en', 'id']),
            ]);
    }

    public function boot(): void
    {
        Column::configureUsing(function (Column $column): void {
            $column
                ->toggleable()
                ->searchable()
                ->sortable();
        });
        Gate::policy(\Awcodes\Curator\Models\Media::class, \App\Policies\MediaPolicy::class);
        Gate::policy(\Spatie\Activitylog\Models\Activity::class, \App\Policies\ActivityPolicy::class);
    }
}
