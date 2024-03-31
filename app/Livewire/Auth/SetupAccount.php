<?php

namespace App\Livewire\Auth;

use Filament\Forms\Form;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components;
use Filament\Pages\SimplePage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Validation\ValidationException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;

/**
 * @property Form $form
 */
class SetupAccount extends SimplePage
{
    use InteractsWithFormActions;
    use WithRateLimiting;

    /**
     * @var view-string
     */
    protected static string $view = 'livewire.auth.setup-account';

    protected ?string $maxWidth = '3xl';

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    public function mount(): void
    {
        if (Filament::auth()->check()) {
            redirect()->intended(Filament::getUrl());
        }

        $this->form->fill();
    }

    public function setupAccount(): ?LoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            throw ValidationException::withMessages([
                'error' => __('filament::login.messages.throttled', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]),
            ]);
        }

        $data = $this->form->getState();

        $user = \App\Models\User::where('username', $data['username'])->first();

        if ($user->email !== $data['email'] || $user->password !== null || ! $user->is_active) {
            throw ValidationException::withMessages([
                'error' => __('auth.verification_failed'),
            ]);
        }

        $user->update([
            'password' => Hash::make($data['password']),
        ]);

        Filament::auth()->login($user);

        session()->regenerate();

        return app(LoginResponse::class);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Components\Section::make([
                    Components\Fieldset::make('verification')
                        ->label(__('auth.verification_required'))
                        ->schema([
                            Components\TextInput::make('username')
                                ->label(__('attr.username'))
                                ->required()
                                ->maxLength(255)
                                ->autocomplete()
                                ->autofocus(),
                            Components\TextInput::make('email')
                                ->label(__('attr.email'))
                                ->required()
                                ->maxLength(255)
                                ->autocomplete(),
                        ])->columns(),
                    Components\Fieldset::make('password_setup')
                        ->label(__('auth.password_setup'))
                        ->schema([
                            Components\TextInput::make('password')
                                ->label(__('filament-panels::pages/auth/login.form.password.label'))
                                ->password()
                                ->confirmed()
                                ->autocomplete('current-password')
                                ->required()
                                ->maxLength(255),
                            Components\TextInput::make('password_confirmation')
                                ->label(__('auth.password_confirmation'))
                                ->password()
                                ->required()
                                ->maxLength(255),
                        ])->columns(),
                ]),
            ])
            ->statePath('data');
    }

    public function getTitle(): string|Htmlable
    {
        return __('auth.setup_title');
    }

    public function getHeading(): string|Htmlable
    {
        return __('auth.setup_title');
    }

    protected function getSetupFormAction(): Action
    {
        return Action::make('setup-account')
            ->label(__('auth.setup_action'))
            ->submit('setupAccount');
    }

    protected function getLoginFormAction(): Action
    {
        return Action::make('login')
            ->button()
            ->outlined()
            ->label(__('filament-panels::pages/auth/login.form.actions.authenticate.label'))
            ->color('gray')
            ->url(filament()->getLoginUrl());
    }

    /**
     * @return array<Action | ActionGroup>
     */
    protected function getFormActions(): array
    {
        return [
            $this->getLoginFormAction(),
            $this->getSetupFormAction(),
        ];
    }

    protected function hasFullWidthFormActions(): bool
    {
        return true;
    }
}
