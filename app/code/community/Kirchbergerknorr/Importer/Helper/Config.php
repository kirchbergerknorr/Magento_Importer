<?php
/**
 * @category    Kirchbergerknorr
 * @package     Kirchbergerknorr/Importer/Helper
 * @subpackage  Config
 * @author      Nick Dilßner <nick.dilssner@kirchbergerknorr.de>
 * @copyright   Copyright (c) 2016 kirchbergerknorr GmbH (http://www.kirchbergerknorr.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Kirchbergerknorr_Importer_Helper_Config
{
    const PATH = 'kirchbergerknorr/importer/';

    // TODO: implement multiple config possibility
    /**
     * get importer config
     *
     * @param   string  $key    config key
     * @param   int     $type   (NOT IMPLEMENTED YET) from which importer config
     * @return  mixed           config value
     */
    public function get($key, $type = 0)
    {
        return Mage::getStoreConfig(self::PATH . $key);
    }
}