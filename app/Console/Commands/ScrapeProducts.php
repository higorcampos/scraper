<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\ScrapeProductsJob;
use App\Services\ScraperService;

class ScrapeProducts extends Command
{
    protected $signature = 'scrape:products';
    protected $description = 'Scrape products from the website';

    protected $scraperService;

    public function __construct(ScraperService $scraperService)
    {
        parent::__construct();
        $this->scraperService = $scraperService;
    }

    public function handle()
    {
        ScrapeProductsJob::dispatch($this->scraperService);
        $this->info('Scraping job dispatched successfully!');
    }
}