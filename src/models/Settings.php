<?php
/**
 * Cache Warmer plugin for Craft CMS 3.x
 *
 * A plugin for running a series of cache warming tasks
 *
 * @link      https://bluemantis.com
 * @copyright Copyright (c) 2020 Bluemantis
 */

namespace bluemantis\cachewarmer\models;

use bluemantis\cachewarmer\CacheWarmer;

use Craft;
use craft\base\Model;

/**
 * @author    Bluemantis
 * @package   CacheWarmer
 * @since     0.1
 */
class Settings extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $itemsPerBatch = 20;

    public $timeBetweenRequests = 0;

    public $enabledSections = [];

    public $enabledProductTypes = [];

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['itemsPerBatch', 'number'],
            ['timeBetweenRequests', 'number'],
        ];
    }
}
