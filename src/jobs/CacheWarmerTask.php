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
use craft\elements\Entry;
use craft\queue\BaseJob;

/**
 * @author    Bluemantis
 * @package   CacheWarmer
 * @since     0.1
 */
class CacheWarmerTask extends BaseJob
{
    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $entryIds = [];
    public $productIds = [];

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function execute($queue)
    {
        $settings = CacheWarmer::getInstance()->getSettings();
        $batchCount = ($settings->itemsPerBatch ? $settings->itemsPerBatch : 1);

        // Chunk off the entries in no more than the max batch number set in the settings
        foreach ($this->entryIds as $siteId => $entryIds) {
            $entryBatches = array_chunk($entryIds, $batchCount);
            foreach ($entryBatches as $entryBatch) {
                // Fire out a queue job
                \Craft::$app->queue->push(new EntryCacheWarmerTask([
                    'entryIds' => $entryBatch,
                    'siteId' => $siteId,
                ]));
            }
        }

        // Chunk off the products in no more than the max batch number set in the settings
        foreach ($this->productIds as $siteId => $productIds) {
            $productBatches = array_chunk($productIds, $batchCount);
            foreach ($productBatches as $productBatch) {
                // Fire out a queue job
                \Craft::$app->queue->push(new ProductCacheWarmerTask([
                    'productIds' => $productBatch,
                    'siteId' => $siteId,
                ]));
            }
        }
    }

    protected function defaultDescription(): string
    {
        return Craft::t('cachewarmer', 'CacheWarmer is warming up');
    }
}
