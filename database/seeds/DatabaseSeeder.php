<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(SyncP1::class);
        $this->call(SyncBPHTB::class);
        $this->call(SyncPBBMonthly::class);
        $this->call(SyncRetribusi::class);
    }
}
