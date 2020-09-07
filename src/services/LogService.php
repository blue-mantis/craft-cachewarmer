<?php
/**
 * Importer plugin for Craft CMS 3.x
 *
 * Import Data
 *
 * @link      bluemantis.com
 * @copyright Copyright (c) 2019 Blue Mantis
 */

namespace bluemantis\cachewarmer\services;

use Craft;
use craft\base\Component;
use Psr\Log\LogLevel;

class LogService extends Component
{
    protected $logPath;
    protected $logFile;

    public function __construct(array $config = [])
    {
        $this->logPath = CRAFT_BASE_PATH . '/storage/logs/cachewarmer';

        if (!is_dir($this->logPath)) {
            // dir doesn't exist, make it
            mkdir($this->logPath);
        }

        parent::__construct($config);
    }

    public function setLogFile($name)
    {
        $this->logFile = $name;
    }

    /**
     * Write to log files
     */
    public function write($messages, $logLevel = LogLevel::INFO, string $logFile = '') : void
    {
        if (!is_array($messages)) {
            $messages = [$messages];
        }

        $logFile = (($logFile) ? $logFile . '-' : '') . date('Y-m-d');

        foreach ($messages as $message) {
            // this is actually quite a messy/verbose log, we don't really need it
            // if (!craft()->isConsole()) ChannelAdvisorPlugin::log($message, $logLevel, true);

            $message = "[" . $logLevel . "] " . date('Y-m-d H:i:s') . "\t" . $message . PHP_EOL;
            file_put_contents($this->logPath . '/' . $logFile . '.log', $message, FILE_APPEND | LOCK_EX);
        }

        //file_put_contents($this->logPath . '/' . $logFile . '.log', PHP_EOL, FILE_APPEND | LOCK_EX);
    }

    /**
     * Get log for specific date
     */
    public function getLog(string $date = null, string $logFile = '', $view = '') : string
    {
        if (!$date) {
            $date = date('Y-m-d');
        }

        $filePath = $this->logPath . '/' . (($logFile) ? $logFile . '-' : '') . (($date) ? $date : date('Y-m-d')). '.log';

        $log = file_exists($filePath) ? trim(file_get_contents($filePath)) : '';

        if ($view === 'errors') {
            $log = $this->filterLogToErrors($log);
        }

        return $log;
    }

    protected function filterLogToErrors($log)
    {
        $newLog = '';
        $rows = explode("\n", $log);

        foreach ($rows as $row) {
            if (strpos($row, '[error]') === 0) {
                $newLog .= $row . "\n";
            }
        }

        return $newLog;
    }
}
