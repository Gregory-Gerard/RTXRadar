<?php

namespace App\Jobs;

use App\Models\ProductItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\HttpClient\HttpClient;

class RueducommerceScraper implements ShouldQueue
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
        $productList = config('scraping.rueducommerce');
        $scrapedProductList = [];

        foreach ($productList as $product) {
            try {
                $productItemList = json_decode(HttpClient::create(['timeout' => 60])->request('GET', $product['url'], [
                    'headers' => [
                        'x-requested-with' => 'XMLHttpRequest'
                    ]
                ])->getContent(false), true)['produits'];
            } catch (\Throwable $e) {
                return;
            }

            foreach ($productItemList as $productItem) {
                switch (strtolower($productItem['Disponibilite'])) {
                    case 'en stock':
                        $state = 'yes';
                        break;
                    default:
                        $state = 'no';
                }

                $scrapedProductList[] = [
                    'product_id' => $product['id'],
                    'seller' => 'rueducommerce',
                    'seller_internal_id' => $productItem['produit_id'],
                    'url' => "https://www.rueducommerce.fr{$productItem['lien']}",
                    'title' => "{$productItem['fournisseur_nom']} {$productItem['produit_nom_nom']}",
                    'price' => (int)round($productItem['prix_ttc']*100),
                    'state' => $state
                ];
            }
        }

        ProductItem::upsert($scrapedProductList, ['seller', 'seller_internal_id'], [
            'url', 'title', 'price', 'state'
        ]);
    }
}
