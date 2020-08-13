<?php

namespace bluemantis\cachewarmer\events;

class CacheWarmEvent extends \yii\base\ModelEvent
{
    public $settings = [];
}
