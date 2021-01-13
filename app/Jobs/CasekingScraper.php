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

class CasekingScraper implements ShouldQueue
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
        $productList = config('scraping.caseking');

        $scrapedProductList = [];

        foreach ($productList as $product) {
            $crawler = $client->request('GET', $product['url']);

            $crawler->filter('.ck_listing .inner')->each(function (/** @var $node Crawler */ $node) use (&$scrapedProductList, $product) {
                $sellerInternalId = $node->filter('.producttitles')->attr('data-id');

                $state = $node->filter('.basketform')->count() ? 'yes' : 'no';

                $scrapedProductList[] = [
                    'product_id' => $product['id'],
                    'seller' => 'caseking',
                    'seller_internal_id' => $sellerInternalId,
                    'url' => $node->filter('a')->link()->getUri(),
                    'title' => $node->filter('.producttitles')->attr('title'),
                    'price' => (int)(round((float)preg_replace(['/[^0-9,]/', '/,/'], ['', '.'], $node->filter('.price')->text()), 2) * 100),
                    'state' => $state
                ];

            });
        }

        ProductItem::upsert($scrapedProductList, ['seller', 'seller_internal_id'], [
            'url', 'title', 'price', 'state'
        ]);
    }
}
