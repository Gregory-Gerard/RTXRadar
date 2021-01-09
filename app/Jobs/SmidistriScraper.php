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

class SmidistriScraper implements ShouldQueue
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
        $productList = config('scraping.smidistri');
        $scrapedProductList = [];

        foreach ($productList as $product) {
            $crawler = $client->request('GET', $product['url']);

            $crawler->filter('#product_list .item')->each(function (/** @var $node Crawler */ $node) use (&$scrapedProductList, $product) {
                try {
                    $stock = $node->filter('.stocks span')->attr('id');
                } catch (\Exception $e) {
                    return;
                }

                switch ($stock) {
                    case 'stock':
                    case 'stocklimite':
                        $state = 'yes';
                        break;
                    default:
                        $state = 'no';
                }

                $scrapedProductList[] = [
                    'product_id' => $product['id'],
                    'seller' => 'smidistri',
                    'seller_internal_id' => sha1($node->filter('a')->first()->link()->getUri()),
                    'url' => $node->filter('a')->first()->link()->getUri(),
                    'title' => $node->filter('h3')->first()->text(),
                    'price' => (int)(((float)str_replace(' ', '', str_replace(',', '.', $node->filter('.price')->first()->text())))*100),
                    'state' => $state
                ];
            });
        }

        ProductItem::upsert($scrapedProductList, ['seller', 'seller_internal_id'], [
            'url', 'title', 'price', 'state'
        ]);
    }
}
