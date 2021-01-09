<?php

namespace App\Jobs;

use App\Models\ProductItem;
use App\Models\PushNotification;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Str;

class ShouldSendPushNotification implements ShouldQueue
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
        // Récupère les produits en stock et dont la dernière notification date d'il y a plus de 15 minutes
        $productItemToSend = ProductItem::where('state', 'yes')->whereDoesntHave('pushNotifications', function (Builder $query) {
            $query->where('updated_at', '>', Carbon::now()->subMinutes(15));
        })->get();

        if ($productItemToSend->isNotEmpty()) {
            $message = "⚠️Produit en stock : ".Str::limit($productItemToSend->pluck('title')->join(', ', ' et '), 20);

            \OneSignal::setParam('priority', 10)->sendNotificationToAll($message);

            PushNotification::upsert(
                $productItemToSend->transform(function ($product) {
                    return [
                        'product_item_id' => $product->id,
                    ];
                })->toArray(),
                [ 'product_item_id']
            );
        }
    }
}
