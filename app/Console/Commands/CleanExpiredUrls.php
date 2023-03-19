<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Url;
class CleanExpiredUrls extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clean:expired-urls';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete expired URLs from database';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $expiredUrls = Url::where('expired_at', '<=', Carbon::now())->get();
        $expiredUrls->each->delete();
        $this->info('Expired URLs deleted successfully.');
    }
}
