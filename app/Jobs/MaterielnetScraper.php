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

class MaterielnetScraper implements ShouldQueue
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
        $productList = config('scraping.materielnet');
        $scrapedProductList = [];

        foreach ($productList as $product) {
            $crawler = $client->request('GET', $product['url']);

            preg_match('/({.*})/i', $crawler->filterXPath('//script[contains(.,"dataLayout.offerListJson")]')->text(), $offerListJson);
            $offerListJson = $offerListJson[1];

            try {
                $priceAndStock = json_decode(HttpClient::create(['timeout' => 60])->request('POST', 'https://www.materiel.net/product-listing/stock-price/', [
                    'headers' => [
                        'x-requested-with' => 'XMLHttpRequest',
                        'content-type' => 'application/x-www-form-urlencoded; charset=UTF-8'
                    ],
                    'body' => "json={$offerListJson}"
                ])->getContent(false), true);
            } catch (\Throwable $e) {
                return;
            }

            $crawler->filter('.c-products-list__row li')->each(function (/** @var $node Crawler */ $node) use (&$scrapedProductList, $product, $priceAndStock) {
                $sellerInternalId = $node->attr('data-offer-id');

                switch (trim(strtolower(substr((new Crawler($priceAndStock['stock'][$sellerInternalId]))->text(), 12)))) {
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

                $priceHtml = new Crawler();
                $priceHtml->addHtmlContent($priceAndStock['price'][$sellerInternalId], 'UTF-8');

                $scrapedProductList[] = [
                    'product_id' => $product['id'],
                    'seller' => 'materielnet',
                    'seller_internal_id' => $sellerInternalId,
                    'url' => $node->filter('a')->first()->link()->getUri(),
                    'title' => $node->filter('h2')->first()->text(),
                    'price' => (int)round((float)preg_replace('/[^0-9\.]/i', '', str_replace('€', '.', $priceHtml->text()))*100),
                    'state' => $state
                ];
            });
        }

        ProductItem::upsert($scrapedProductList, ['seller', 'seller_internal_id'], [
            'url', 'title', 'price', 'state'
        ]);
    }
}
