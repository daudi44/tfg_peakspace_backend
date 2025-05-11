<?php

namespace App\Console\Commands;

use App\Models\Movement;
use App\Models\Subscription;
use Illuminate\Console\Command;

class ApplySubscriptionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:apply-subscriptions-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = now();
        $todayDay = $today->day;
        $subscriptions = Subscription::whereDay('start_date', $todayDay)
            ->where(function ($query) use ($today) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', $today);
            })
            ->get();

        foreach ($subscriptions as $subscription) {
            Movement::create([
                'name' => $subscription->name,
                'amount' => $subscription->amount,
                'user_id' => $subscription->user_id,
                'category_id' => $subscription->category_id,
                'type' => 0,
                'created_at' => $today,
            ]);
        }

        $this->info("Processed " . $subscriptions->count() . " subscriptions for today.");
    }

}
