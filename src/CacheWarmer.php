<?php

namespace bluemantis\cachewarmer;

use bluemantis\cachewarmer\jobs\EntryCacheWarmerTask;
use bluemantis\cachewarmer\services\CacheWarm;
use bluemantis\cachewarmer\models\Settings;

use bluemantis\cachewarmer\services\LogService;
use Craft;
use craft\base\Plugin;
use craft\commerce\elements\Product;
use craft\commerce\Plugin as CommercePlugin;
use craft\elements\Entry;
use craft\events\ElementEvent;
use craft\helpers\ElementHelper;
use craft\services\Elements;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\console\Application as ConsoleApplication;
use craft\web\UrlManager;
use craft\events\RegisterUrlRulesEvent;

use yii\base\Event;

class CacheWarmer extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var CacheWarmer
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $schemaVersion = '0.1';

    /**
     * @var bool
     */
    public $hasCpSettings = true;

    /**
     * @var bool
     */
    public $hasCpSection = false;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        $this->setComponents([
            'cacheWarm' => CacheWarm::class,
            'logService' => LogService::class,
        ]);

        if (Craft::$app instanceof ConsoleApplication) {
            $this->controllerNamespace = 'bluemantis\cachewarmer\console\controllers';
        }

        $settings = $this->getSettings();

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['siteActionTrigger1'] = 'cachewarmer/default';
            }
        );

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['cpActionTrigger1'] = 'cachewarmer/default/do-something';
            }
        );

        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                }
            }
        );

        if ($settings->warmChangedElements) {
            Event::on(
                Elements::class,
                Elements::EVENT_AFTER_SAVE_ELEMENT,
                function (ElementEvent $event) {
                    $element = $event->element;

                    if (!ElementHelper::isDraftOrRevision($element)) {
                        Craft::$app->getQueue()->push(new EntryCacheWarmerTask([
                            'entryIds' => $element->id,
                            'siteId' => $element->siteId,
                        ]));
                    }
                }
            );
        }

        Craft::info(
            Craft::t(
                'cachewarmer',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }

    /**
     * @inheritdoc
     */
    protected function settingsHtml(): string
    {
        $commercePlugin = Craft::$app->getPlugins()->getPlugin('commerce');

        $sections = [];
        $productTypes = [];

        $sites = Craft::$app->getSites()->getAllSites();
        $allSections = Craft::$app->getSections()->getAllSections();

        if ($commercePlugin) {
            $allProductTypes = CommercePlugin::getInstance()->getProductTypes()->getAllProductTypes();
        }

        foreach ($sites as $site) {
            // Separate each section by site
            $sections[$site->id] = [
                'site' => $site,
                'sections' => [],
            ];

            foreach ($allSections as $section) {
                // Check if the section has a URI format, otherwise there'll be no page to cache
                if (isset($section->siteSettings[$site->id]) && $section->siteSettings[$site->id] && $section->siteSettings[$site->id]->uriFormat) {
                    $sections[$site->id]['sections'][$section->handle] = [];
                    $sections[$site->id]['sections'][$section->handle]['section'] = $section;
                    // Total entries for this section
                    $sections[$site->id]['sections'][$section->handle]['count'] = Entry::find()->siteId($site->id)->sectionId($section->id)->count();
                }
            }

            if ($commercePlugin) {
                $productTypes[$site->id] = [
                    'site' => $site,
                    'types' => [],
                ];
                foreach ($allProductTypes as $productType) {
                    // Check if the product type has a URI format, otherwise there'll be no page to cache
                    if (isset($productType->siteSettings[$site->id]) && $productType->siteSettings[$site->id] && $productType->siteSettings[$site->id]->uriFormat) {
                        $productTypes[$site->id]['types'][$productType->handle] = [];
                        $productTypes[$site->id]['types'][$productType->handle]['type'] = $productType;
                        // Total products for this product type
                        $productTypes[$site->id]['types'][$productType->handle]['count'] = Product::find()->typeId($productType->id)->count();
                    }
                }
            }
        }

        return Craft::$app->view->renderTemplate(
            'cachewarmer/settings',
            [
                'settings' => $this->getSettings(),
                'sectionData' => $sections,
                'productTypesData' => $productTypes,
            ]
        );
    }
}
