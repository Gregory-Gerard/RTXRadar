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

class CloudmarktScraper implements ShouldQueue
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
        $productList = config('scraping.cloudmarkt');

        $scrapedProductList = [];

        foreach ($productList as $product) {
            $crawler = $client->request('GET', $product['url']);

            $crawler->filter('.listing .product--box')->each(function (/** @var $node Crawler */ $node) use (&$scrapedProductList, $product) {
                preg_match('/articleID\/([0-9]+)/', $node->filter('.product--price-compare')->attr('action'), $sellerInternalId);
                $sellerInternalId = $sellerInternalId[1];

                $state = $node->filter('.buybox--form')->count() ? 'yes' : 'no';

                $scrapedProductList[] = [
                    'product_id' => $product['id'],
                    'seller' => 'cloudmarkt',
                    'seller_internal_id' => $sellerInternalId,
                    'url' => $node->filter('a')->link()->getUri(),
                    'title' => "{$node->filter('.product--supplier')->text()} {$node->filter('.product--title')->text()}",
                    'price' => (int)(round((float)preg_replace(['/[^0-9,]/', '/,/'], ['', '.'], $node->filter('.product--price')->text()), 2) * 100),
                    'state' => $state
                ];

            });
        }

        ProductItem::upsert($scrapedProductList, ['seller', 'seller_internal_id'], [
            'url', 'title', 'price', 'state'
        ]);
    }
}
