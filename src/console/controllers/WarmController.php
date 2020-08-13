<?php
/**
 * Cache Warmer plugin for Craft CMS 3.x
 *
 * A plugin for running a series of cache warming tasks
 *
 * @link      https://bluemantis.com
 * @copyright Copyright (c) 2020 Bluemantis
 */

namespace bluemantis\cachewarmer\console\controllers;

use bluemantis\cachewarmer\CacheWarmer;

use yii\console\Controller;

class WarmController extends Controller
{
    public function actionRun()
    {
        CacheWarmer::$plugin->cacheWarmer->run();
        die('The CacheWarmer is warming up');
    }
}
