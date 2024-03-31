<?php

use App\Http\Livewire\Auth\SetupAccount;
use App\Http\Livewire\Auth\Login;
use Livewire\Livewire;

test('Complete Account page is accessible', function () {
    Livewire::test(SetupAccount::class)
        ->assertStatus(200);
});

test('User can complete their account and login', function () {
    $user = createUser([
        'password' => null,
    ]);

    $password = 'demo123';

    Livewire::test(SetupAccount::class)
        ->fillForm([
            'username' => $user->username,
            'email' => $user->email,
            'password' => $password,
            'password_confirmation' => $password,
        ])
        ->call('completeAccount')
        ->assertHasNoFormErrors()
        ->assertRedirect('/dashboard');

    Livewire::test(Login::class)
        ->fillForm([
            'username' => $user->username,
            'password' => $password,
        ])
        ->call('authenticate')
        ->assertRedirect('/dashboard');
});

it('shows error when account is already active', function () {
    $user = createUser();

    $password = 'demo123';

    Livewire::test(SetupAccount::class)
        ->fillForm([
            'username' => $user->username,
            'email' => $user->email,
            'password' => $password,
            'password_confirmation' => $password,
        ])
        ->call('completeAccount')
        ->assertHasFormErrors()
        ->assertSee('verify your credentials. Please contact your supervisor')
        ->assertNoRedirect();
});

it('shows error when invalid information is filled', function () {
    $user = createUser([
        'password' => null,
    ]);

    $password = 'demo123';

    Livewire::test(SetupAccount::class)
        ->fillForm([
            'username' => $user->username,
            'email' => 'random_email@example.com',
            'password' => $password,
            'password_confirmation' => $password,
        ])
        ->call('completeAccount')
        ->assertHasFormErrors()
        ->assertSee('verify your credentials. Please contact your supervisor')
        ->assertNoRedirect();
});

it('shows error when non completed account has inactive status', function () {
    $user = createUser([
        'password' => null,
        'is_active' => false,
    ]);

    $password = 'demo123';

    Livewire::test(SetupAccount::class)
        ->fillForm([
            'username' => $user->username,
            'email' => $user->email,
            'password' => $password,
            'password_confirmation' => $password,
        ])
        ->call('completeAccount')
        ->assertHasFormErrors()
        ->assertSee('verify your credentials. Please contact your supervisor')
        ->assertNoRedirect();
});

it('redirects to login page when button is pressed', function () {
    Livewire::test(SetupAccount::class)
        ->call('showLoginPage')
        ->assertRedirect('/login')
        ->assertSee('Login')
        ->assertSee('Username')
        ->assertSee('Password');
});
