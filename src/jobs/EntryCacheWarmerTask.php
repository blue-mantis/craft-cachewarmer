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
        sleep(10);
        return;
        $entries = Entry::find()->siteId($this->siteId)->id($this->entryIds)->all();
        CacheWarmer::$plugin->cacheWarm->elements($entries, $queue);
    }

    protected function defaultDescription(): string
    {
        $site = Craft::$app->getSites()->getSiteById($this->siteId);
        $count = (is_array($this->entryIds) ? count($this->entryIds) : 1);
        return Craft::t('cachewarmer', 'Cache warming ' . $count . ' entry page' . ($count===1 ? '' : 's') . ' for ' . $site->name);
    }
}
