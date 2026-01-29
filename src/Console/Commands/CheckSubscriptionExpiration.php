<?php

namespace Thebrightlabs\QiCard\Console\Commands;

use Illuminate\Console\Command;
use Thebrightlabs\QiCard\Models\Subscription;
use Thebrightlabs\QiCard\QiCardGateway;

class CheckSubscriptionExpiration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qiCard:expiration-check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process Subscription Expiration every 5 minutes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Processing subscription expiration checks...');
        $expiredSubscriptions = Subscription::query()
            ->where('status','paid')
            ->where('end_date','<',now())
            ->update(['status' => 'expired']);
        $this->info('Iraq payments processing completed.');
    }

}
