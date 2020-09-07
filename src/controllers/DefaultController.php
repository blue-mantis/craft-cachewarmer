<?php
/**
 * Cache Warmer plugin for Craft CMS 3.x
 *
 * A plugin for running a series of cache warming tasks
 *
 * @link      https://bluemantis.com
 * @copyright Copyright (c) 2020 Bluemantis
 */

namespace bluemantis\cachewarmer\controllers;

use bluemantis\cachewarmer\CacheWarmer;

use Composer\Cache;
use Craft;
use craft\web\Controller;

/**
 * @author    Bluemantis
 * @package   CacheWarmer
 * @since     0.1
 */
class DefaultController extends Controller
{
    //TODO For CI we need to add a key in the settings that we can check on here, before this is made allowAnonymous againgi
    /*protected $allowAnonymous = ['run'];*/

    public function actionRun()
    {
        CacheWarmer::$plugin->cacheWarmer->run();
        die('The CacheWarmer is warming up');
    }
}
