# Cache Warmer plugin for Craft CMS 3.x

A plugin for running a series of cache warming tasks

![Screenshot](resources/img/plugin-logo.png)

## Requirements

This plugin requires Craft CMS 3.0.0-beta.23 or later.

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require https://github.com/blue-mantis/cachewarmer/cachewarmer

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for Cache Warmer.

## Cache Warmer Overview

This plugin provides a bunch of cache warming tasks. More details to follow.

## Configuring Cache Warmer

Go to Settings, and look for the cachewarmer icon at the bottom. More details on options to follow.

## Using Cache Warmer

There's currently 2 ways to kick off a cachewarm:

You can call the service method directly to run a cachewarm based on the configuration set in the settings above

    \bluemantis\cachewarmer\CacheWarmer::$plugin->cacheWarm->run();
    
Or you can pipe in an array of elements to only warm those

    \bluemantis\cachewarmer\CacheWarmer::$plugin->cacheWarm->elements([$element]);
    
(in the second example the $queue parameter can be safely ignored, this is there to update progress when this is running as a queued job)

## Cache Warmer Roadmap

*  Adding an option to automatically warm the cache of an element on save

Brought to you by [Bluemantis](https://bluemantis.com)
