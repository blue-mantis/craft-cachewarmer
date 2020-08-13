<?php
/**
 * Cache Warmer plugin for Craft CMS 3.x
 *
 * A plugin for running a series of cache warming tasks
 *
 * @link      https://bluemantis.com
 * @copyright Copyright (c) 2020 Bluemantis
 */

namespace bluemantis\cachewarmer\assetbundles\cachewarmer;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    Bluemantis
 * @package   CacheWarmer
 * @since     0.1
 */
class CacheWarmerAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = "@bluemantis/cachewarmer/assetbundles/cachewarmer/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/CacheWarmer.js',
        ];

        $this->css = [
            'css/CacheWarmer.css',
        ];

        parent::init();
    }
}
