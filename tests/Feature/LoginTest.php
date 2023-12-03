<?php

use Livewire\Livewire;
use App\Livewire\Auth\Login;

test('Login page is accessible', function () {
    Livewire::test(Login::class)
        ->assertStatus(200);
});

test('User can login', function () {
    Livewire::test(Login::class)
        ->fillForm([
            'username' => 'admin',
            'password' => 'password',
        ])
        ->call('authenticate')
        ->assertHasNoFormErrors()
        ->assertRedirect('/');
});

test('User is redirected to complete account page when account isnt completed', function () {
    $user = createUser([
        'password' => null,
    ]);

    Livewire::test(Login::class)
        ->fillForm([
            'username' => $user->username,
            'password' => 'any_password',
        ])
        ->call('authenticate')
        ->assertRedirect('/users/complete-account');
});

it('shows error when credential is invalid', function () {
    Livewire::test(Login::class)
        ->fillForm([
            'username' => 'non_existing_user',
            'password' => 'invalid_password',
        ])
        ->call('authenticate')
        ->assertHasFormErrors()
        ->assertSee('These credentials do not match our records.')
        ->assertNoRedirect();
});

it('redirects to complete account page when button is pressed', function () {
    Livewire::test(Login::class)
        ->call('showCompleteAccountPage')
        ->assertRedirect('/users/complete-account');
});
