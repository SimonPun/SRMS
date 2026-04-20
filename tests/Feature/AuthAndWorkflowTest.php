<?php

namespace Tests\Feature;

use App\Models\RequestUpdate;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthAndWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_redirects_to_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('login'));
    }

    public function test_client_can_log_in_and_submit_a_request(): void
    {
        $client = User::factory()->create([
            'role' => 'client',
            'password' => 'password',
        ]);

        $this->post(route('login.submit'), [
            'email' => $client->email,
            'password' => 'password',
        ])->assertRedirect(route('user.dashboard'));

        $this->actingAs($client)->post(route('requests.store'), [
            'title' => 'Broken projector',
            'description' => 'The projector in lab 2 is not turning on.',
            'category' => 'IT',
            'location' => 'Lab 2',
            'priority' => 'high',
        ])->assertSessionHas('success');

        $this->assertDatabaseHas('service_requests', [
            'user_id' => $client->id,
            'title' => 'Broken projector',
            'category' => 'IT',
            'location' => 'Lab 2',
            'priority' => 'high',
            'status' => 'pending',
        ]);
    }

    public function test_client_can_register_for_a_new_account(): void
    {
        $this->post(route('register.submit'), [
            'name' => 'New Client',
            'email' => 'newclient@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect(route('user.dashboard'));

        $this->assertDatabaseHas('users', [
            'email' => 'newclient@example.com',
            'role' => 'client',
        ]);
    }

    public function test_registration_requires_matching_password_confirmation(): void
    {
        $this->from(route('register'))
            ->post(route('register.submit'), [
                'name' => 'Broken Registration',
                'email' => 'broken@example.com',
                'password' => 'password123',
                'password_confirmation' => 'wrong-password',
            ])
            ->assertRedirect(route('register'));

        $this->assertDatabaseMissing('users', [
            'email' => 'broken@example.com',
        ]);
    }

    public function test_admin_can_assign_request_to_service_staff(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $staff = User::factory()->create(['role' => 'service_staff']);
        $otherStaff = User::factory()->create(['role' => 'service_staff']);
        $client = User::factory()->create(['role' => 'client']);
        $serviceRequest = ServiceRequest::create([
            'user_id' => $client->id,
            'title' => 'Network issue',
            'description' => 'The office network is unstable.',
            'priority' => 'medium',
        ]);

        $this->actingAs($admin)->post(route('admin.requests.assign', $serviceRequest->id), [
            'user_ids' => [$staff->id, $otherStaff->id],
        ])->assertSessionHas('success');

        $this->assertDatabaseHas('service_requests', [
            'id' => $serviceRequest->id,
            'assigned_to' => $staff->id,
            'status' => 'in_progress',
        ]);
        $this->assertDatabaseHas('service_request_staff', [
            'service_request_id' => $serviceRequest->id,
            'user_id' => $staff->id,
            'staff_status' => 'pending',
        ]);
        $this->assertDatabaseHas('service_request_staff', [
            'service_request_id' => $serviceRequest->id,
            'user_id' => $otherStaff->id,
            'staff_status' => 'pending',
        ]);

        $this->assertDatabaseHas('request_updates', [
            'service_request_id' => $serviceRequest->id,
            'updated_by' => $admin->id,
            'new_status' => 'in_progress',
        ]);
    }

    public function test_service_staff_can_update_status_for_assigned_request(): void
    {
        $staff = User::factory()->create(['role' => 'service_staff']);
        $client = User::factory()->create(['role' => 'client']);
        $serviceRequest = ServiceRequest::create([
            'user_id' => $client->id,
            'title' => 'Leaking tap',
            'description' => 'The sink tap is leaking in room 4.',
            'priority' => 'low',
            'status' => 'in_progress',
        ]);
        $serviceRequest->assignedStaff()->attach($staff->id);
        $serviceRequest->update(['assigned_to' => $staff->id]);

        $this->actingAs($staff)->patch(route('staff.requests.status', $serviceRequest->id), [
            'status' => 'completed',
            'note' => 'Issue resolved',
        ])->assertSessionHas('success');

        $this->assertDatabaseHas('service_requests', [
            'id' => $serviceRequest->id,
            'status' => 'completed',
        ]);

        $this->assertDatabaseHas('request_updates', [
            'service_request_id' => $serviceRequest->id,
            'updated_by' => $staff->id,
            'old_status' => 'in_progress',
            'new_status' => 'completed',
            'note' => 'Issue resolved',
        ]);

        $this->assertDatabaseHas('service_request_staff', [
            'service_request_id' => $serviceRequest->id,
            'user_id' => $staff->id,
            'staff_status' => 'completed',
        ]);
    }

    public function test_overall_status_remains_in_progress_until_all_assigned_staff_complete(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $staffOne = User::factory()->create(['role' => 'service_staff']);
        $staffTwo = User::factory()->create(['role' => 'service_staff']);
        $client = User::factory()->create(['role' => 'client']);
        $serviceRequest = ServiceRequest::create([
            'user_id' => $client->id,
            'title' => 'Door lock issue',
            'description' => 'Main door lock needs replacement.',
            'priority' => 'high',
            'status' => 'pending',
        ]);

        $this->actingAs($admin)->post(route('admin.requests.assign', $serviceRequest->id), [
            'user_ids' => [$staffOne->id, $staffTwo->id],
        ])->assertSessionHas('success');

        $this->actingAs($staffOne)->patch(route('staff.requests.status', $serviceRequest->id), [
            'status' => 'completed',
            'note' => 'My part is done',
        ])->assertSessionHas('success');

        $this->assertDatabaseHas('service_requests', [
            'id' => $serviceRequest->id,
            'status' => 'in_progress',
        ]);

        $this->actingAs($staffTwo)->patch(route('staff.requests.status', $serviceRequest->id), [
            'status' => 'completed',
            'note' => 'Done from my side too',
        ])->assertSessionHas('success');

        $this->assertDatabaseHas('service_requests', [
            'id' => $serviceRequest->id,
            'status' => 'completed',
        ]);
    }

    public function test_client_can_view_own_request_details(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $request = ServiceRequest::create([
            'user_id' => $client->id,
            'title' => 'Heating problem',
            'description' => 'The heating is not working.',
            'category' => 'Maintenance',
            'location' => 'Office 3',
            'priority' => 'medium',
        ]);

        $this->actingAs($client)
            ->get(route('requests.show', $request))
            ->assertOk()
            ->assertSee('Heating problem')
            ->assertSee('Office 3');
    }

    public function test_client_cannot_view_another_clients_request_details(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $otherClient = User::factory()->create(['role' => 'client']);
        $request = ServiceRequest::create([
            'user_id' => $otherClient->id,
            'title' => 'Private issue',
            'description' => 'Should not be visible.',
            'category' => 'Other',
            'priority' => 'low',
        ]);

        $this->actingAs($client)
            ->get(route('requests.show', $request))
            ->assertForbidden();
    }

    public function test_admin_request_filters_work(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = User::factory()->create(['role' => 'client']);

        ServiceRequest::create([
            'user_id' => $client->id,
            'title' => 'IT issue',
            'description' => 'Computer not working',
            'category' => 'IT',
            'location' => 'Lab 4',
            'priority' => 'high',
            'status' => 'pending',
        ]);

        ServiceRequest::create([
            'user_id' => $client->id,
            'title' => 'Cleaning request',
            'description' => 'Hallway needs attention',
            'category' => 'Cleaning',
            'location' => 'Hallway',
            'priority' => 'low',
            'status' => 'completed',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.requests', ['category' => 'IT', 'status' => 'pending']))
            ->assertOk()
            ->assertSee('IT issue')
            ->assertDontSee('Cleaning request');
    }

    public function test_client_is_redirected_away_from_admin_routes(): void
    {
        $client = User::factory()->create(['role' => 'client']);

        $this->actingAs($client)
            ->get(route('admin.requests'))
            ->assertRedirect(route('user.dashboard'));
    }

    public function test_admin_can_change_a_users_role(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'client']);

        $this->actingAs($admin)
            ->patch(route('admin.users.role', $user), [
                'role' => 'service_staff',
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'role' => 'service_staff',
        ]);
    }

    public function test_admin_can_create_service_staff_from_manage_users(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->post(route('admin.users.store'), [
                'name' => 'New Staff Member',
                'email' => 'newstaff@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'email' => 'newstaff@example.com',
            'role' => 'service_staff',
        ]);
    }

    public function test_staff_creation_requires_unique_email(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        User::factory()->create([
            'role' => 'service_staff',
            'email' => 'duplicate@example.com',
        ]);

        $this->actingAs($admin)
            ->from(route('admin.users.create'))
            ->post(route('admin.users.store'), [
                'name' => 'Duplicate Staff',
                'email' => 'duplicate@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ])
            ->assertRedirect(route('admin.users.create'));
    }

    public function test_admin_role_cannot_be_changed_from_manage_users(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $otherAdmin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($otherAdmin)
            ->patch(route('admin.users.role', $admin), [
                'role' => 'client',
            ])
            ->assertSessionHas('error');

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'role' => 'admin',
        ]);
    }

    public function test_user_can_mark_notifications_as_read(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = User::factory()->create(['role' => 'client']);
        $serviceRequest = ServiceRequest::create([
            'user_id' => $client->id,
            'title' => 'Printer issue',
            'description' => 'Printer is jammed.',
            'priority' => 'medium',
            'status' => 'pending',
        ]);

        RequestUpdate::create([
            'service_request_id' => $serviceRequest->id,
            'updated_by' => $admin->id,
            'old_status' => 'pending',
            'new_status' => 'in_progress',
            'note' => 'Technician assigned',
        ]);

        $this->actingAs($client)
            ->get(route('notifications.recent'))
            ->assertOk()
            ->assertJsonPath('count', 1);

        $this->actingAs($client)
            ->post(route('notifications.read-all'))
            ->assertOk()
            ->assertJsonPath('count', 0);

        $this->actingAs($client)
            ->get(route('notifications.recent'))
            ->assertOk()
            ->assertJsonPath('count', 0);
    }

    public function test_user_can_dismiss_a_notification(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = User::factory()->create(['role' => 'client']);
        $serviceRequest = ServiceRequest::create([
            'user_id' => $client->id,
            'title' => 'Projector issue',
            'description' => 'Projector bulb needs replacement.',
            'priority' => 'high',
            'status' => 'pending',
        ]);

        $update = RequestUpdate::create([
            'service_request_id' => $serviceRequest->id,
            'updated_by' => $admin->id,
            'old_status' => 'pending',
            'new_status' => 'in_progress',
            'note' => 'Parts ordered',
        ]);

        $this->actingAs($client)
            ->delete(route('notifications.dismiss', $update))
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->actingAs($client)
            ->get(route('notifications.recent'))
            ->assertOk()
            ->assertJsonCount(0, 'notifications');
    }

    public function test_logged_in_user_can_update_profile_and_password(): void
    {
        $client = User::factory()->create([
            'role' => 'client',
            'password' => 'oldpassword123',
        ]);

        $this->actingAs($client)
            ->put(route('profile.update'), [
                'name' => 'Updated Client',
                'email' => 'updatedclient@example.com',
                'password' => 'newpassword123',
                'password_confirmation' => 'newpassword123',
            ])
            ->assertRedirect(route('profile.show'))
            ->assertSessionHas('success');

        $updatedUser = $client->fresh();
        $this->assertSame('Updated Client', $updatedUser->name);
        $this->assertSame('updatedclient@example.com', $updatedUser->email);
        $this->assertTrue(Hash::check('newpassword123', $updatedUser->password));
    }

    public function test_user_can_request_password_reset_link(): void
    {
        $client = User::factory()->create([
            'role' => 'client',
            'email' => 'resetme@example.com',
        ]);

        $this->post(route('password.email'), [
            'email' => $client->email,
        ])->assertSessionHas('success');

        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => $client->email,
        ]);
    }
}
