<?php

namespace bluemantis\cachewarmer\models;

use Craft;
use craft\base\Model;

class Settings extends Model
{
    public $itemsPerBatch = 20;

    public $timeBetweenRequests = 0;

    public $warmChangedElements = false;

    public $enabledSections = [];

    public $enabledCategoryGroups = [];

    public $enabledProductTypes = [];

    public $customUrls;

    public function rules()
    {
        return [
            ['itemsPerBatch', 'number'],
            ['timeBetweenRequests', 'number'],
        ];
    }
}
