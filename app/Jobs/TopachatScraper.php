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

class TopachatScraper implements ShouldQueue
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
        $productList = config('scraping.topachat');
        $scrapedProductList = [];

        foreach ($productList as $product) {
            $crawler = $client->request('GET', $product['url']);

            $crawler->filter('.produits > article > section')->each(function (/** @var $node Crawler */ $node) use (&$scrapedProductList, $product) {
                switch ($node->attr('class')) {
                    case 'en-stock':
                    case 'en-stock-limite':
                        $state = 'yes';
                        break;
                    case 'en-rupture':
                        $state = 'no';
                        break;
                    case 'dispo-sous-7-jours':
                    case 'dispo-entre-7-15-jours':
                    case 'dispo-plus-15-jours':
                        $state = 'soon';
                        break;
                    default:
                        $state = 'no';
                }

                $scrapedProductList[] = [
                    'product_id' => $product['id'],
                    'seller' => 'topachat',
                    'seller_internal_id' => $node->attr('id'),
                    'url' => $node->filter('a')->first()->link()->getUri(),
                    'title' => $node->filter('h3')->first()->text(),
                    'price' => (int)(((float)$node->filter('.price')->first()->text())*100),
                    'state' => $state
                ];
            });
        }

        ProductItem::upsert($scrapedProductList, ['seller', 'seller_internal_id'], [
            'url', 'title', 'price', 'state'
        ]);
    }
}
