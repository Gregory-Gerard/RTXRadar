<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class Scraper implements ShouldQueue
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
        TopachatScraper::dispatch();
        MaterielnetScraper::dispatch();
        LdlcScraper::dispatch();
        SmidistriScraper::dispatch();
        CybertekScraper::dispatch();
        RueducommerceScraper::dispatch();
        AlternateScraper::dispatch();
        ArtencraftScraper::dispatch();
        CasekingScraper::dispatch();
        CloudmarktScraper::dispatch();
    }
}
