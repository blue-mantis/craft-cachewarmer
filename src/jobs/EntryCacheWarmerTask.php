<?php

namespace bluemantis\cachewarmer\jobs;

use bluemantis\cachewarmer\CacheWarmer;

use Craft;
use craft\elements\Entry;
use craft\queue\BaseJob;

class EntryCacheWarmerTask extends BaseJob
{
    public $entryIds = [];

    public $siteId = 1;

    public function execute($queue)
    {
        $entries = Entry::find()->siteId($this->siteId)->id($this->entryIds)->all();
        CacheWarmer::$plugin->cacheWarm->elements($entries, $queue);
    }

    protected function defaultDescription(): string
    {
        $site = Craft::$app->getSites()->getSiteById($this->siteId);
        return Craft::t('cachewarmer', 'Caching ' . count($this->entryIds) . ' entry pages for ' . $site->name);
    }
}
