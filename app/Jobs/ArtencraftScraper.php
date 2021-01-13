<?php

namespace App\Jobs;

use App\Models\ProductItem;
use Goutte\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;

class ArtencraftScraper implements ShouldQueue
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
        $client = new Client(HttpClient::create(['timeout' => 60]));
        $websiteList = config('scraping.artencraft');

        $scrapedProductList = [];

        foreach ($websiteList as $website => $productList) {
            foreach ($productList as $product) {
                $crawler = $client->request('GET', $product['url']);

                $crawler->filter('.product-overview li .product-data-wrap')->each(function (/** @var $node Crawler */ $node) use (&$scrapedProductList, $website, $product) {
                    preg_match('/onProductClick\(\'([0-9]+)\'\)/', $node->filter('a')->first()->attr('onclick'), $sellerInternalId);
                    $sellerInternalId = $sellerInternalId[1];

                    $state = $node->filter('.add-to-cart')->count() ? 'yes' : 'no';

                    $scrapedProductList[] = [
                        'product_id' => $product['id'],
                        'seller' => $website,
                        'seller_internal_id' => $sellerInternalId,
                        'url' => $node->filter('a')->link()->getUri(),
                        'title' => $node->filter('.product-name')->text(),
                        'price' => (int)(round((float)preg_replace(['/[^0-9,]/', '/,/'], ['', '.'], $node->filter('.product-price')->text()), 2) * 100),
                        'state' => $state
                    ];

                });
            }
        }

        ProductItem::upsert($scrapedProductList, ['seller', 'seller_internal_id'], [
            'url', 'title', 'price', 'state'
        ]);
    }
}
