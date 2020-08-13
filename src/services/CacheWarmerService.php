<?php

namespace bluemantis\cachewarmer\services;

use bluemantis\cachewarmer\CacheWarmer;
use bluemantis\cachewarmer\jobs\CacheWarmerTask;
use craft\base\Component;
use craft\commerce\elements\Product;
use craft\elements\Entry;
use craft\queue\QueueInterface;
use GuzzleHttp\Client;
use Psr\Log\LogLevel;

class CacheWarmerService extends Component
{
    public function run()
    {
        $settings = CacheWarmer::getInstance()->getSettings();
        $enabledSections = [];
        $enabledProductTypes = [];

        $entriesToCache = [];
        $productsToCache = [];

        $entries = [];

        // Loop through all relevant sections, grouped by site
        foreach ($settings->enabledSections as $siteId => $site) {
            // Filter out any that aren't enabled
            $enabledSections[$siteId] = array_filter($settings->enabledSections[$siteId], function ($item) {
                if (!empty($item['enabled'])) {
                    return true;
                }
            });

            // Assuming there are any sections enabled, grab the ids
            if (count($enabledSections[$siteId])) {
                $entriesToCache[$siteId] = Entry::find()->siteId($siteId)->section(array_keys($enabledSections[$siteId]))->limit(null)->ids();
            }
        }

        // Loop through all relevant product types, grouped by site
        foreach ($settings->enabledProductTypes as $siteId => $site) {
            $enabledProductTypes[$siteId] = array_filter($settings->enabledProductTypes[$siteId], function ($item) {
                if (!empty($item['enabled'])) {
                    return true;
                }
            });

            // Assuming there are any product types enabled, grab the ids
            if (count($enabledProductTypes[$siteId])) {
                $productsToCache[$siteId] = Product::find()->siteId($siteId)->typeId(array_keys($enabledProductTypes[$siteId]))->limit(null)->ids();
            }
        }

        // Push a cache warmer warmer job to divvy up he tasks outside of the main thread
        \Craft::$app->queue->push(new CacheWarmerTask([
            'productIds' => $productsToCache,
            'entryIds' => $entriesToCache,
        ]));
    }

    public function warmCache(array $elements, QueueInterface $queue = null) {
        $settings = CacheWarmer::getInstance()->getSettings();
        $total = count($elements);
        $count = 1;

        foreach ($elements as $element) {
            try {
                // We don't care about self signed certs, at least I dont think we do
                $client = new Client(['verify' => false]);
                $url = $element->getUrl();
                // If an item without a url made it through, ignore
                if ($url) {
                    $client->request('GET', $url, ['connect_timeout' => 30]);
                    CacheWarmer::$plugin->logService->write($url . ' has been cached');

                    // if a sleep has been set in the settings, run it
                    if ($settings->timeBetweenRequests) {
                        sleep($settings->timeBetweenRequests);
                        CacheWarmer::$plugin->logService->write('Waiting ' . $settings->timeBetweenRequests . ' seconds for the next request');
                    }
                }

                // If we've been passed a valid queue object, update the progress
                if ($queue) {
                    $progress = floor(($count*100)/$total);
                    $queue->setProgress($progress, $progress.'% complete');
                }
                $count++;
            } catch (\Exception $e) {
                CacheWarmer::$plugin->logService->write('Failed to cache ' . $url . ': ' . $e->getMessage(), LogLevel::ERROR);
            }
        }
    }
}
