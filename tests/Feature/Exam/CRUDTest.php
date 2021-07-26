<?php

namespace Tests\Feature\Exam;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class CRUDTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Create route
     *
     * @test
     */
    public function create_route()
    {
        $user = \App\Models\User::factory()->create();

        $this->actingAs($user)
            ->post(route('logs.store'), [
                'log' => 'Logging from create route test',
                'day' => '2021-01-01',
            ]);

        $this->assertDatabaseHas('logs', [
            'user_id' => $user->id,
            'log'     => 'Logging from create route test',
            'day'     => '2021-01-01 00:00:00',
        ]);
    }

    /**
     * Validates the payload
     * - log: should be required
     * - day : should be required and have a valid date
     *
     * @test
     */
    public function validate_the_payload()
    {
        $user = \App\Models\User::factory()->create();

        $this->actingAs($user);

        $this->postJson(route('logs.store'))
            ->assertJsonValidationErrors([
                'log' => __('validation.required', ['attribute' => 'log']),
                'day' => __('validation.required', ['attribute' => 'day']),
            ]);

        $this->postJson(route('logs.store'), ['day' => 'invalid-date'])
            ->assertJsonValidationErrors([
                'day' => __('validation.date', ['attribute' => 'day']),
            ]);
    }

    /**
     * Refactor the code and implement Route Model Binding
     * - You should apply the refactor and this test still
     *   need to pass
     *
     * @test
     */
    public function implement_route_model_binding()
    {
        $user   = \App\Models\User::factory()->create();
        $log    = \App\Models\Log::factory()->create();

        $this->actingAs($user);

        $this->put(route('logs.update', $log), [
            'log' => 'Updating the text',
        ]);

        $this->assertDatabaseHas('logs', [
            'id'  => $log->id,
            'log' => 'Updating the text',
        ]);
    }

    /**
     * Create a Policy to authorize only the user owner
     * of the log to be able to delete the log.
     *
     * @test
     */
    public function use_policy_to_authorize_deletion()
    {
        $user1 = \App\Models\User::factory()->create();
        $user2 = \App\Models\User::factory()->create();

        $log = \App\Models\Log::factory()->create([
            'user_id' => $user2->id,
        ]);

        $this->actingAs($user1);

        $this->actingAs($user1)
            ->deleteJson(route('logs.delete', $log))
            ->assertForbidden();
    }

    /**
     * Apply Soft Delete
     *
     * @test
     */
    public function apply_soft_delete()
    {
        $user = \App\Models\User::factory()->create();

        $log = \App\Models\Log::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user)
            ->deleteJson(route('logs.delete', $log));

        $this->assertSoftDeleted('logs', ['id' => $log->id]);
    }

    /**
     * Create a custom rule that will block the word SHIT
     * if exists on the log field
     *
     * @test
     */
    public function custom_rule()
    {
        $user = \App\Models\User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('logs.store'), [
                'log' => 'Developers that get SHIT done',
                'day' => '2020-01-01',
            ])
            ->assertJsonValidationErrors([
                'log' => "Bad word! Don't use SHIT. Please!!!",
            ]);

        $this->assertCount(0, $user->logs);
    }

    /**
     * Create a middleware that will block a user with
     * a name "Jane Doe" to create Logs
     *
     * @test
     */
    public function use_middleware_to_block_access()
    {
        $user = \App\Models\User::factory()->create([
            'name' => 'Jane Doe',
        ]);

        $this->actingAs($user)
            ->postJson(route('logs.store'), [
                'log' => 'Developers that get things done',
                'day' => '2021-01-01',
            ])->assertUnauthorized();
    }

    /**
     * Dispatch an event after a creation of a Log
     *
     * @test
     */
    public function dispatch_an_event()
    {
        Event::fake();

        $user = \App\Models\User::factory()->create();

        $this->actingAs($user)
            ->post(route('logs.store'), [
                'log' => 'Logging from create route test',
                'day' => '2021-01-01',
            ]);

        Event::assertDispatched(\App\Events\LogCreated::class);
    }
}
