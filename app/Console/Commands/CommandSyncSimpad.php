<?php

namespace App\Console\Commands;

use App\Jobs\SyncSimpad;
use App\Jobs\SyncPBBDaily;
use App\Jobs\SyncWebRDaily;
use App\Jobs\SyncBPHTBDaily;
use App\Jobs\SyncSimpadPiutang;
use Illuminate\Console\Command;

class CommandSyncSimpad extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:syncsimpad';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync data from simpad to sipari.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        SyncSimpad::dispatch();
        SyncBPHTBDaily::dispatch();
        SyncWebRDaily::dispatch();
        SyncPBBDaily::dispatch();
        SyncSimpadPiutang::dispatch();
    }
}
