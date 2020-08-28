<?php

namespace bluemantis\cachewarmer\jobs;

use bluemantis\cachewarmer\CacheWarmer;

use Craft;
use craft\commerce\elements\Product;
use craft\queue\BaseJob;

class ProductCacheWarmerTask extends BaseJob
{
    public $productIds = [];

    public $siteId = 1;

    public function execute($queue)
    {
        $products = Product::find()->siteId($this->siteId)->id($this->entryIds)->all();
        CacheWarmer::$plugin->cacheWarm->elements($products);
    }

    protected function defaultDescription(): string
    {
        return Craft::t('cachewarmer', 'Caching ' . count($this->productIds) . ' product pages');
    }
}
