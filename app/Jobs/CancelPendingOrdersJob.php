<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Order;



class CancelPendingOrdersJob implements ShouldQueue
{
    
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Order::query()
            ->where('status', 'pending')
            ->where('created_at', '<=', now()->subMinutes(30))
            ->update([
                'status' => 'canceled',
                'canceled_reason' => 'انتهت مهلة قبول الطلب',
            ]);
    }
}
