<?php
/**
 * @category    Kirchbergerknorr
 * @package     Kirchbergerknorr/Importer/Model
 * @subpackage  Config
 * @author      Nick Dilßner <nick.dilssner@kirchbergerknorr.de>
 * @copyright   Copyright (c) 2016 kirchbergerknorr GmbH (http://www.kirchbergerknorr.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Kirchbergerknorr_Importer_Model_Config
{
    const IMPORTER_NAMESPACE = 'kirchbergerknorr_importer';

    /**
     * @var mixed[] config data
     */
    protected $config;

    /**
     * @var Kirchbergerknorr_Importer_Model_Factory helper factory
     */
    protected $factory;

    /**
     * Kirchbergerknorr_Importer_Model_Config constructor.
     *
     * @param   mixed[] $config importer config
     */
    public function __construct($config)
    {
        $this->config = $config;
        $this->factory = Mage::getModel('kirchbergerknorr_importer/factory');
    }

    // TODO: refactor if get as simple get- value is needed, else remove this
    /**
     * get raw values from config without magic
     *
     * @param   string  $key    config key
     *
     * @return  mixed           raw value
     */
    public function getValue($key)
    {
        if (($value = $this->value($key))) {
            return $value;
        }
        return null;
    }

    /**
     * get value from config for given key - uses magic
     *
     * @param   string  $key        config key
     * @param   mixed[] $arguments  (optional) additional parameters for magic object creation
     *
     * @return  mixed               value from config
     */
    public function get($key, $arguments = array())
    {
        return $this->config($key, $arguments);
    }

    /**
     * get dynamic value from config - replaces YYYY, MM, DD with date
     *
     * @param   string  $key    config key
     *
     * @return  mixed           value, replaced with date if it is a string
     */
    public function dynamic($key)
    {
        $value = $this->get($key);
        if ($value && is_string($value)) {
            $date = new DateTime('now');
            // TODO: optimize - currently all occurrences are replaced
            $value = str_replace(
                array('YYYY', 'MM', 'DD'),
                array($date->format('Y'), $date->format('m'), $date->format('d')),
                $value
            );
        }
        return $value;
    }

    /**
     * magic config return values depending on type
     *
     * @param   string      $key            key in config which is wanted
     * @param   mixed[]     $arguments      (optional) additional arguments for objects
     * @param   string[]    $identifiers    (optional) identifiers for array handling -> which key is needed to create a object, else it returns only a array
     *
     * @return  mixed                       magic values
     */
    protected function config($key, $arguments = array(), $identifiers = array('model'))
    {
        if ($value = $this->value($key)) {
            switch (gettype($value)) {
                case 'array':
                    // exclude mapping, to have a free namespace
                    if ($key !== 'mapping') {
                        foreach ($identifiers as $identifier) {
                            if (!empty($value[$identifier]) && is_string($value[$identifier])) {;
                                $arguments = (!empty($value['arguments'])) ? array_merge($arguments, $value['arguments']) : $arguments;
                                $type = end(explode('/', $key));
                                return $this->factory->get($type, $value[$identifier], $arguments);
                            }
                        }
                    }
                    return $value;
                    break;
                case 'string':
                    // TODO: think about it, possible that it tries to crerate to much models, but config is only read one time
                    if ($model = $this->factory->get($key, $value, $arguments)) {
                        return $model;
                    }
                    return $value;
                    break;
                case 'object':
                    // TODO: implement stuff, if needed
                    break;
                case 'NULL':
                    // TODO: implement, if needed
                    break;
                default:
                    return $value;
                    break;
            }
            return $value;
        }
        return null;
    }

    /**
     * get config value from key - can handle sub-keys splitted with '/'
     *
     * @param   string      $key    key to get
     *
     * @return  mixed|null          value if found
     */
    protected function value($key)
    {
        $config = $this->config;
        foreach (explode('/', $key) as $key) {
            if (isset($config[$key])) {
                $config = $config[$key];
            } else {
                return null;
            }
        }
        return $config;
    }
}