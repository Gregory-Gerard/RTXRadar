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
        // Récupère les produits en stock, dont aucune notification n'a été envoyée et qui ne dépasse pas le prix max
        $productItemToSend = ProductItem::where(function (Builder $query) {
            $query->where('state', 'yes')
                ->orWhere('state', 'soon');
        })->whereHas('product', function (Builder $query) {
            $query->where('push', true)
                ->whereColumn('price', '<=', DB::raw('max_price_threshold*100'));
        })->doesntHave('pushNotifications')->get();

        if ($productItemToSend->isNotEmpty()) {
            $message = "⚠️ Produit en stock : ".Str::limit($productItemToSend->pluck('title')->join(', ', ' et '), 20);

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

        // Supprime les notifications des produits qui sont repassé hors stock
        PushNotification::whereHas('productItem', function ($query) {
            $query->where('state', 'no');
        })->delete();
    }
}
