<?php

namespace App\Jobs;

use App\Models\Product;
use App\Models\ProductItem;
use Goutte\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;

class AlternateScraper implements ShouldQueue
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
        $websiteList = config('scraping.alternate');

        $scrapedProductList = [];

        foreach ($websiteList as $website => $productList) {
            foreach ($productList as $product) {
                $crawler = $client->request('GET', $product['url']);

                $crawler->filter('.listingContainer .listRow')->each(function (/** @var $node Crawler */ $node) use (&$scrapedProductList, $website, $product) {
                    parse_str(parse_url($node->filter('.openProductCompare')->link()->getUri(), PHP_URL_QUERY), $qs);
                    $sellerInternalId = $qs['articleId'];
                    $state = $node->filter('.stockStatus')->matches('.available_unsure') !== true && strpos($node->filter('.stockStatus')->attr('class'), 'available') !== false || strpos($node->filter('.stockStatus')->attr('class'), 'ship_only_fast') !== false ? 'yes' : 'no';

                    $scrapedProductList[] = [
                        'product_id' => $product['id'],
                        'seller' => $website,
                        'seller_internal_id' => $sellerInternalId,
                        'url' => $node->filter('a')->link()->getUri(),
                        'title' => $node->filter('.product .name')->text(),
                        'price' => (int)round((float)preg_replace('/[^0-9,]/', '', $node->filter('.price')->text()), 2)*100,
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
