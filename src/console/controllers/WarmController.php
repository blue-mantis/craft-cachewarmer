<?php

namespace bluemantis\cachewarmer\console\controllers;

use bluemantis\cachewarmer\CacheWarmer;

use yii\console\Controller;

class WarmController extends Controller
{
    public function actionRun()
    {
        CacheWarmer::$plugin->cacheWarm->run();
        die('The CacheWarmer is warming up');
    }
}
