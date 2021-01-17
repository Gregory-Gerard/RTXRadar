<?php

namespace App\Jobs;

use App\Models\ProductItem;
use App\Models\PushNotification;
use Carbon\Carbon;
use DB;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Str;

class DeleteOldProductItems implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Les produits qui n'ont pas été mis à jour depuis plus de 5 minutes ne sont plus listé, donc on supprime
        ProductItem::where('updated_at', '<=', Carbon::now()->subMinutes(5))->delete();
    }
}
