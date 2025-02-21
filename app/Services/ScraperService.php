<?php

namespace App\Services;

use App\Traits\ScraperTrait;
use App\Repositories\ProductRepository;
use App\Repositories\CategoryRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Mail\ScrapingCompleted;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Collection;

class ScraperService
{
    use ScraperTrait;

    protected $productRepository;
    protected $categoryRepository;

    public function __construct(ProductRepository $productRepository, CategoryRepository $categoryRepository)
    {
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
    }

    public function scrape()
    {
        $cacheKey = 'scraped_products';
        $cacheTTL = 3600;

        if (Cache::has($cacheKey)) {
            $products = Cache::get($cacheKey);
            Log::info('Loaded products from cache.');
        } else {
            try {
                $categories = $this->scrapeCategories();
                $products = new Collection();

                foreach ($categories as $category) {
                    $categoryProducts = $this->scrapeCategoryProducts($category['link'], $category['id']);
                    $products = $products->merge($categoryProducts);
                }

                Cache::put($cacheKey, $products, $cacheTTL);
                Log::info('Scraped products and stored in cache.');

                // Enviar e-mail após o término do scraping
                Mail::to('recipient@example.com')->send(new ScrapingCompleted());
            } catch (\Exception $e) {
                Log::error('Scraping failed: ' . $e->getMessage());
            }
        }

        return $products;
    }

    protected function scrapeCategories()
    {
        $crawler = $this->scrapePage(env('SCRAPER_URL'));
        $categories = [];

        $crawler->filter('.side_categories ul li ul li a')->each(function ($node) use (&$categories) {
            $name = trim($node->text());
            $link = str_replace('../', '', $node->attr('href'));

            $category = $this->categoryRepository->updateOrCreate([
                'name' => $name,
                'link' => $link,
            ]);

            Log::info('Category created or updated', ['name' => $name, 'link' => $link]);

            $categories[] = [
                'id' => $category->id,
                'name' => $name,
                'link' => $link,
            ];
        });

        return $categories;
    }

    protected function scrapeCategoryProducts($categoryLink, $categoryId)
    {
        $products = new Collection();
        $page = 1;
        $hasNextPage = true;

        while ($hasNextPage) {
            $url = $this->buildCategoryUrl($categoryLink, $page);
            $crawler = $this->scrapePage($url);

            $crawler->filter('.product_pod')->each(function ($node) use (&$products, $categoryId) {
                $productData = $this->extractProductData($node, $categoryId);
                if ($this->isValidProductData($productData)) {
                    $products->push($productData);
                }
            });

            $hasNextPage = $crawler->filter('.next')->count() > 0;
            $page++;
        }

        $this->saveProducts($products);

        return $products;
    }

    protected function buildCategoryUrl($categoryLink, $page)
    {
        $url = env('SCRAPER_URL_BASE') . $categoryLink;
        if ($page > 1) {
            $url = str_replace('index.html', 'page-' . $page . '.html', $url);
        }
        return $url;
    }

    protected function extractProductData($node, $categoryId)
    {
        $title = $node->filter('h3 a')->attr('title');
        $price = str_replace('£', '', $node->filter('.price_color')->text());
        $link = str_replace('../../../', '', $node->filter('h3 a')->attr('href'));
        $image = str_replace('../../../../', '', $node->filter('.image_container a img')->attr('src'));

        $productCrawler = $this->scrapePage(env('SCRAPER_URL_CATALOGUE') . $link);
        $descriptionNode = $productCrawler->filter('#product_description ~ p')->first();
        $description = $descriptionNode->count() ? $descriptionNode->text() : '';

        return [
            'title' => $title,
            'price' => $price,
            'link' => $link,
            'category_id' => $categoryId,
            'image' => $image,
            'description' => $description,
        ];
    }

    protected function isValidProductData($productData)
    {
        return !empty($productData['title']) && !empty($productData['price']) && !empty($productData['link']);
    }

    protected function saveProducts($products)
    {
        $products->each(function ($productData) {
            $this->productRepository->updateOrCreate($productData);
            Log::info('Product created or updated', $productData);
        });
    }
}