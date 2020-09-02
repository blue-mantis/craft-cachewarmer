<?php

namespace bluemantis\cachewarmer\jobs;

use bluemantis\cachewarmer\CacheWarmer;

use Craft;
use craft\elements\Category;
use craft\queue\BaseJob;

class CategoryCacheWarmerTask extends BaseJob
{
    public $categoryIds = [];

    public $siteId = 1;

    public function execute($queue)
    {
        $categories = Category::find()->siteId($this->siteId)->id($this->categoryIds)->all();
        CacheWarmer::$plugin->cacheWarm->elements($categories, $queue);
    }

    protected function defaultDescription(): string
    {
        $site = Craft::$app->getSites()->getSiteById($this->siteId);
        $count = (is_array($this->categoryIds) ? count($this->categoryIds) : 1);
        return Craft::t('cachewarmer', 'Cache warming ' . $count . ' category page' . ($count===1 ? '' : 's') . ' for ' . $site->name);
    }
}
