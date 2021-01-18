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

class CybertekScraper implements ShouldQueue
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
        $productList = config('scraping.cybertek');
        $scrapedProductList = [];

        foreach ($productList as $product) {
            $crawler = $client->request('GET', $product['url']);

            $crawler->filter('#content_product .prod_background')->each(function (/** @var $node Crawler */ $node) use (&$scrapedProductList, $product) {
                // $stock = strtolower($node->filter('.center-dispo > span > span')->text('Non disponible'));

                // Si le bouton panier est visible, il est en stock (cybertek à désactivé le bouton panier pour le moment donc s'il revient elles seront commandables)
                if (stripos($node->filter('.panier')->attr('style'), 'none') === false && $node->filter('.panier a')->attr('class') !== 'disable') {
                    $state = 'yes';
                } else {
                    $state = 'no';
                }

                /*switch ($node->filter('.panier')->attr('style')) {
                    case 'en stock':
                    case 'dernière pièce':
                    case 'uniquement en magasin':
                        $state = 'yes';
                        break;
                    case '4 à 6 jours':
                    case '7 à 14 jours':
                    case 'plus de 15 jours':
                        $state = 'soon';
                        break;
                    default:
                        $state = 'no';
                }*/

                $scrapedProductList[] = [
                    'product_id' => $product['id'],
                    'seller' => 'cybertek',
                    'seller_internal_id' => substr($node->filter('.product_ref')->text(), 6),
                    'url' => $node->filter('a')->first()->link()->getUri(),
                    'title' => $node->filter('h2')->first()->text(),
                    'price' => (int)(((float)str_replace(' ', '', str_replace('€', '.', $node->filter('.price_prod ')->first()->text())))*100),
                    'state' => $state
                ];
            });
        }

        ProductItem::upsert($scrapedProductList, ['seller', 'seller_internal_id'], [
            'url', 'title', 'price', 'state'
        ]);
    }
}
