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
use craft\elements\Entry;
use craft\queue\BaseJob;

/**
 * @author    Bluemantis
 * @package   CacheWarmer1
 * @since     0.1
 */
class EntryCacheWarmerTask extends BaseJob
{
    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $entryIds = [];

    public $siteId = 1;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function execute($queue)
    {
        $entries = Entry::find()->siteId($this->siteId)->id($this->entryIds)->all();
        CacheWarmer::$plugin->cacheWarmerService->warmCache($entries, $queue);
    }

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function defaultDescription(): string
    {
        $site = Craft::$app->getSites()->getSiteById($this->siteId);
        return Craft::t('cachewarmer', 'Caching ' . count($this->entryIds) . ' entry pages for ' . $site->name);
    }
}
