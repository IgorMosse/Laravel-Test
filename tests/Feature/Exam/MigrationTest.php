<?php

namespace Tests\Feature\Exam;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MigrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Create logs table
     *
     * @test
     */
    public function create_logs_table()
    {
        $this->assertTrue(
            Schema::hasTable('logs')
        );
    }

    /**
     * Add columns to your table:
     * user_id : int not null
     * log: text not null
     * day: date not null
     * created_at: date not null
     * updated_at: date not null
     *
     * @test
     */
    public function create_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('logs', [
                'id',
                'user_id',
                'log',
                'day',
                'created_at',
                'updated_at',
            ])
        );
    }

    /**
     * Create a foreign key that will connect user_id with users table.
     * Make sure to create an index for this column.
     *
     * @test
     */
    public function create_foreign_key_and_index()
    {
        $constrain = collect(DB::select("PRAGMA index_list(logs)"))
            ->where('name', '=', 'logs_user_id_index')->first();

        $this->assertNotNull(
            $constrain
        );
    }
}
