<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/admin')->assertRedirect(route('admin.login'));
    }

    public function test_superadmin_sees_dashboard(): void
    {
        $admin = User::where('role', User::ROLE_SUPERADMIN)->first();
        $this->actingAs($admin)->get('/admin')->assertOk()->assertSee('Dashboard');
    }

    public function test_editor_cannot_access_bookings(): void
    {
        $editor = User::where('role', User::ROLE_EDITOR)->first();
        $this->actingAs($editor)->get(route('admin.bookings.index'))->assertForbidden();
    }

    public function test_editor_can_access_places(): void
    {
        $editor = User::where('role', User::ROLE_EDITOR)->first();
        $this->actingAs($editor)->get(route('admin.places.index'))->assertOk();
    }

    public function test_reception_cannot_manage_users(): void
    {
        $reception = User::where('role', User::ROLE_RECEPTION)->first();
        $this->actingAs($reception)->get(route('admin.users.index'))->assertForbidden();
    }

    public function test_login_works_with_valid_credentials(): void
    {
        $this->post(route('admin.login.attempt'), [
            'email' => 'admin@cassinocentrale.it',
            'password' => 'password',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticated();
    }
}
