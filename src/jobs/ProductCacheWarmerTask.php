<?php
/**
 * Cache Warmer plugin for Craft CMS 3.x
 *
 * A plugin for running a series of cache warming tasks
 *
 * @link      https://bluemantis.com
 * @copyright Copyright (c) 2020 Bluemantis
 */

namespace bluemantis\cachewarmer\jobs;

use bluemantis\cachewarmer\CacheWarmer;

use Craft;
use craft\commerce\elements\Product;
use craft\queue\BaseJob;

/**
 * @author    Bluemantis
 * @package   CacheWarmer
 * @since     0.1
 */
class ProductCacheWarmerTask extends BaseJob
{
    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $productIds = [];

    public $siteId = 1;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function execute($queue)
    {
        $products = Product::find()->siteId($this->siteId)->id($this->entryIds)->all();
        CacheWarmer::$plugin->cacheWarmerService->warmCache($products);
    }

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function defaultDescription(): string
    {
        return Craft::t('cachewarmer', 'Caching ' . count($this->productIds) . ' product pages');
    }
}
