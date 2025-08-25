<?php

namespace TheBrightLabs\IraqPayments\Console\Commands;

use Illuminate\Console\Command;
use Thebrightlabs\IraqPayments\Models\Subscription;
use Thebrightlabs\IraqPayments\QiCardGateway;

class CheckPaymentStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qiCard:status-check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process Iraq payments every 10 minutes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Processing Iraq payments...');

        // user exited website after opening the form, means hes no longer needs it mark it as cancelled.
        $subscriptionsThatInPending = Subscription::query()->where('status', 'pending')->get();
        $qiCardGateway = app(QiCardGateway::class);
        foreach ($subscriptionsThatInPending as $subscriptionThatInPending) {
            // since the payment not finished, we gonna finish it.
            // in this fn we'll handle both cases if not succeded, whatever its mark it as cancelled, (left webiste or process went wrong..)
            $qiCardGateway->handleFinishedPayment($subscriptionThatInPending->payment_id);
        }
        $this->info('Iraq payments processing completed.');
    }
}
