<?php

namespace App\Console\Commands;

use App\Models\Firm;
use App\Models\Rubric;
use Illuminate\Console\Command;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'for tests supervisor';

    public function handle(): void
    {
        dd(Firm::all()[20]->phones);
        dd(123);
    }
}
