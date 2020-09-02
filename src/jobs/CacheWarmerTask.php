<?php

namespace bluemantis\cachewarmer\jobs;

use bluemantis\cachewarmer\CacheWarmer;

use Craft;
use craft\queue\BaseJob;

class CacheWarmerTask extends BaseJob
{
    public $categoryIds = [];
    public $entryIds = [];
    public $productIds = [];

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

        // Chunk off the entries in no more than the max batch number set in the settings
        foreach ($this->categoryIds as $siteId => $categoryIds) {
            $categoryBatches = array_chunk($categoryIds, $batchCount);
            foreach ($categoryBatches as $categoryBatch) {
                // Fire out a queue job
                \Craft::$app->queue->push(new CategoryCacheWarmerTask([
                    'categoryIds' => $categoryBatch,
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
