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
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class LdlcScraper implements ShouldQueue
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
        $productList = config('scraping.ldlc');
        $scrapedProductList = [];

        foreach ($productList as $product) {
            $crawler = $client->request('GET', $product['url']);

            $priceAndStockRaw = $crawler->filterXPath('//script[contains(.,"replaceWith")]')->last()->html();

            $crawler->filter('.listing-product > ul > li.pdt-item')->each(function (/** @var $node Crawler */ $node) use (&$scrapedProductList, $product, $priceAndStockRaw) {
                $sellerInternalId = $node->attr('data-id');

                preg_match('/\$\("#pdt-'.$sellerInternalId.' \.price"\)\.replaceWith\(\'<div class="price"><div class="price">([0-9&nbsp;]+)&euro;<sup>([0-9]+)\'\);/m', $priceAndStockRaw, $price);
                preg_match('/\$\("#pdt-'.$sellerInternalId.' \.stocks \.stock-web div"\)\.replaceWith\(\'<div class="modal-stock-web pointer stock stock-.+" data-stock-web=".+"><span>(.+)\'\);/m', $priceAndStockRaw, $stock);


                $price = (int)preg_replace('/[^\d]/', '', $price[1]).$price[2];
                $stock = trim(str_replace('<em>', '', strtolower($stock[1])));

                switch ($stock) {
                    case 'en stock':
                        $state = 'yes';
                        break;
                    case 'rupture':
                        $state = 'no';
                        break;
                    case 'sous 7 jours':
                    case 'entre 7/15 jours':
                    case '+ de 15 jours':
                        $state = 'soon';
                        break;
                    default:
                        $state = 'no';
                }

                $scrapedProductList[] = [
                    'product_id' => $product['id'],
                    'seller' => 'ldlc',
                    'seller_internal_id' => $sellerInternalId,
                    'url' => $node->filter('a')->first()->link()->getUri(),
                    'title' => $node->filter('h3')->first()->text(),
                    'price' => $price,
                    'state' => $state
                ];
            });
        }

        ProductItem::upsert($scrapedProductList, ['seller', 'seller_internal_id'], [
            'url', 'title', 'price', 'state'
        ]);
    }
}
