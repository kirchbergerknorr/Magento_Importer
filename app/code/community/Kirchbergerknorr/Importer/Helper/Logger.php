<?php
/**
 * @category    Kirchbergerknorr
 * @package     Kirchbergerknorr/Importer/Helper
 * @subpackage  Logger
 * @author      Nick Dilßner <nick.dilssner@kirchbergerknorr.de>
 * @copyright   Copyright (c) 2016 kirchbergerknorr GmbH (http://www.kirchbergerknorr.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

// TODO: validate if interface is needed
// TODO: get calling method
/**
 * Class Kirchbergerknorr_PcoInterface_Helper_Logger    for logging
 */
class Kirchbergerknorr_Importer_Helper_Logger
{
    // TODO: config
    /**
     * @var string  log destination path
     */
    private static $destination = 'importer/import';

    /**
     * @var bool    set if debug is enabled for interface
     */
    protected static $debug = null;

    /**
     * @var string  suffix for logging file, will be created in constructor
     */
    protected $suffix = '';

    /**
     * Kirchbergerknorr_Importer_Helper_Logger constructor.
     *
     * @param   string[]    $arguments  for file suffix
     */
    public function __construct($arguments = array())
    {
        $path = array();
        if (isset($arguments['type'])) {
            $path[] = $arguments['type'];
        }
        if (isset($arguments['name'])) {
            $path[] = $arguments['name'];
        }

        $this->suffix = implode('_', $path);
    }

    /**
     * Log given message
     *
     * @param   string  $message    message to log
     * @param   string  $type       (optional) logging type, for different file location
     *
     * @return  Kirchbergerknorr_Importer_Helper_Logger
     */
    public function log($message, $type = '')
    {
        // TODO: Db logging and file logging
        if($message) {
            // Log for admin Backend
            echo $message . "\n";
            Mage::log($message . "\n", null, static::$destination . $this->getSuffix($type) . '.log', true);
        }
        return $this;
    }

    /**
     * Debug Log given message if debug is enabled in config
     *
     * @param   string|mixed                            $message    message to log
     * @param   string                                  $type       (optional) logging type, for different file location
     *
     * @return  Kirchbergerknorr_Importer_Helper_Logger             for chaining
     */
    public function debug($message, $type = '')
    {
        if(self::isDebug() && $message) {
            Mage::log(((is_string($message)) ? $message : var_export($message, true)) . "\n", null, static::$destination . $this->getSuffix($type) . '_debug.log', true);
        }
        return $this;
    }

    // TODO: think about a own exception log
    /**
     * @param   Exception                               $exception  exception to log
     *
     * @return  Kirchbergerknorr_Importer_Helper_Logger             for chaining
     */
    public function exception($exception)
    {
        Mage::logException($exception);
        return $this;
    }

    /**
     * is debug enabled
     *
     * @return  bool    true if enabled, else false
     */
    public static function isDebug()
    {
        if (self::$debug === null) {
            // TODO: create config
            self::$debug = true;
//            self::$debug = (bool) Mage::getStoreConfig('kirchbergerknorr/importer/debug_enabled');
        }
        return self::$debug;
    }

    /**
     * get file suffix
     *
     * @param   string  $type   (optional) additional suffix for file
     *
     * @return  string          file suffix
     */
    protected function getSuffix($type = '')
    {
        $suffix = array();
        if ($this->suffix) {
            $suffix[] = strtolower($this->suffix);
        }
        if ($type) {
            $suffix[] = strtolower($type);
        }
        return (($suffix) ? '_' . implode('_', $suffix) : '');
    }
}