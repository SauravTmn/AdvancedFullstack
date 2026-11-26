<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class Workshop4AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_authentication_owner_isolation_and_logout(): void
    {
        $this->post('/register', [
            'name' => 'Workshop User',
            'email' => 'workshop@example.test',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ])->assertRedirect(route('courses.index'));

        $user = User::where('email', 'workshop@example.test')->firstOrFail();
        $this->assertSame('user', $user->role);
        $this->assertTrue(Hash::check('Password123!', $user->password));

        $course = $user->courses()->create([
            'name' => 'Owned Course',
            'duration' => 2,
            'fee' => 100,
            'difficulty' => 'Easy',
        ]);

        $this->assertDatabaseHas('courses', [
            'id' => $course->id,
            'user_id' => $user->id,
        ]);

        $otherUser = User::factory()->create();
        $this->actingAs($otherUser)
            ->get(route('courses.show', $course))
            ->assertForbidden();

        $this->post('/logout')
            ->assertRedirect(route('home'));

        $this->get(route('courses.index'))->assertRedirect(route('login'));
    }

    public function test_admin_can_view_another_users_course(): void
    {
        $owner = User::factory()->create(['role' => 'user']);
        $course = $owner->courses()->create([
            'name' => 'Admin Visible',
            'duration' => 3,
            'fee' => 150,
            'difficulty' => 'Medium',
        ]);

        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('courses.show', $course))
            ->assertOk()
            ->assertSee('Admin Visible');
    }

    public function test_protected_routes_redirect_guests_and_login_rejects_invalid_credentials(): void
    {
        $this->get(route('courses.index'))->assertRedirect(route('login'));
        $this->get(route('students.index'))->assertRedirect(route('login'));

        $this->from(route('login'))
            ->post('/login', [
                'email' => 'missing@example.test',
                'password' => 'wrong-password',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');
    }
}
