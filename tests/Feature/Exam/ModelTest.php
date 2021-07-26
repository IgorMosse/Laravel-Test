<?php

namespace Tests\Feature\Exam;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ModelTest extends TestCase
{
    /**
     * Create Log Model
     *
     * @test
     */
    public function create_a_model()
    {
        $this->assertTrue(class_exists('App\Models\Log'));
    }

    /**
     * Create relationships between User and Logs
     *
     * @test
     */
    public function relationship_with_the_user()
    {
        $dailyLog       = new \App\Models\Log();
        $relationship   = $dailyLog->user();

        $this->assertEquals(BelongsTo::class, get_class($relationship), 'logs->user()');

        $user           = new \App\Models\User();
        $relationship   = $user->logs();

        $this->assertEquals(HasMany::class, get_class($relationship), 'user->logs()');
    }

    /**
     * Create factories for User and Log
     *
     * @test
     */
    public function create_factories()
    {
        $user = \App\Models\User::factory()->create();
        \App\Models\Log::factory()->create(['user_id' => $user->id]);

        $this->assertCount(1, \App\Models\Log::all());
    }

    /**
     * Implement Model Query Scope to filter Log for today
     *
     * @test
     */
    public function implement_query_scope()
    {
        $user = \App\Models\User::factory()->create();
        \App\Models\Log::factory()->count(3)->create([
            'day'     => now()->subDay(),
            'user_id' => $user->id,
        ]);
        \App\Models\Log::factory()->count(3)->create([
            'day'     => now(),
            'user_id' => $user->id,
        ]);

        $todaysLog = $user->logs()->fromToday()->get();

        $this->assertCount(3, $todaysLog);
    }

    /**
     * Create a get mutator on User's model to transform
     * the return from "joe doe" to "Joe Doe"
     *
     * @test
     */
    public function use_get_mutator()
    {
        $user = \App\Models\User::factory()->make();

        $user->name = 'joe doe';

        $this->assertEquals('Joe Doe', $user->name);
    }

    /**
     * Create a set mutator on User's model to transform
     * the password to a hash string when setting the password
     *
     * @test
     */
    public function use_set_mutator()
    {
        $user = \App\Models\User::factory()->make();

        $user->password = 'secret';

        $this->assertTrue(Hash::check('secret', $user->password));
    }

    /**
     * When retrieving the day from a Log it should return an instance of Carbon
     *
     * @test
     */
    public function date_should_be_a_carbon_instance()
    {
        $log = \App\Models\Log::factory()->make([
            'day' => '2020-02-02',
        ]);

        $this->assertEquals(Carbon::class, get_class($log->day));
    }
}
