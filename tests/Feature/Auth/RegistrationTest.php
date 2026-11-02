<?php

use Illuminate\Support\Facades\Http;
use Laravel\Fortify\Features;

beforeEach(function () {
    $this->skipUnlessFortifyHas(Features::registration());
});

test('registration screen redirects to login when registration is disabled', function () {
    config(['auth.registration_enabled' => false]);

    $response = $this->get(route('register'));

    $response->assertRedirect(route('login'))
        ->assertSessionHas('status', 'Pendaftaran mandiri sedang dinonaktifkan. Silakan hubungi administrator.');
});

test('registration store request redirects to login when registration is disabled', function () {
    config(['auth.registration_enabled' => false]);

    $response = $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'blocked@example.com',
        'phone' => '081234567890',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertRedirect(route('login'))
        ->assertSessionHas('status', 'Pendaftaran mandiri sedang dinonaktifkan. Silakan hubungi administrator.');

    $this->assertGuest();
    $this->assertDatabaseMissing('users', ['email' => 'blocked@example.com']);
});

test('welcome page hides register link when registration is disabled', function () {
    config(['auth.registration_enabled' => false]);

    $response = $this->get(route('home'));

    $response->assertOk()
        ->assertDontSee('href="'.route('register').'"', false);
});

test('registration screen can be rendered when registration is enabled', function () {
    config(['auth.registration_enabled' => true]);

    $response = $this->get(route('register'));

    $response->assertOk();
});

test('new users can register and are redirected to whatsapp verification when registration is enabled', function () {
    config(['auth.registration_enabled' => true]);
    Http::fake();

    $response = $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'phone' => '081234567890',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('verification.whatsapp'));

    $this->assertAuthenticated();
});

test('welcome page shows register link when registration is enabled', function () {
    config(['auth.registration_enabled' => true]);

    $response = $this->get(route('home'));

    $response->assertOk()
        ->assertSee(route('register'));
});
