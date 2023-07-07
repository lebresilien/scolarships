<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Extension;

class DailyExtension extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'extensions:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'set extensions state ';

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
     * @return int
     */
    public function handle()
    {
        $extensions = Extension::whereStatus(true)->get();

        foreach($extensions as $extension) {
            if($extension->valid_until_at->lt(Carbon::now()->format('Y-m-d')))
            $extension->update(["state" => false]);
        }
    }
}
