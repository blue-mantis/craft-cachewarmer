<?php

namespace bluemantis\cachewarmer\jobs;

use bluemantis\cachewarmer\CacheWarmer;

use Craft;
use craft\elements\Entry;
use craft\queue\BaseJob;

class CustomUrlCacheWarmerTask extends BaseJob
{
    public $customUrls = [];

    public function execute($queue)
    {
        CacheWarmer::$plugin->cacheWarm->urls($this->customUrls, $queue);
    }

    protected function defaultDescription(): string
    {
        $count = (is_array($this->customUrls) ? count($this->customUrls) : 1);
        return Craft::t('cachewarmer', 'Cache warming ' . $count . ' custom URL' . ($count===1 ? '' : 's'));
    }
}
