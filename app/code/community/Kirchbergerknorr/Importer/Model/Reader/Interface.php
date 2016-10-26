<?php
/**
 * @category    Kirchbergerknorr
 * @package     Kirchbergerknorr/Importer/Model/Reader
 * @subpackage  Interface
 * @author      Nick Dilßner <nick.dilssner@kirchbergerknorr.de>
 * @copyright   Copyright (c) 2016 kirchbergerknorr GmbH (http://www.kirchbergerknorr.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
interface Kirchbergerknorr_Importer_Model_Reader_Interface
{
    /**
     * get value from data for given key
     *
     * @param   string  $key    key to get from data
     *
     * @return  mixed           value
     */
    public function __get($key);

    /**
     * set data
     * 
     * @param   mixed                                               $data   data to set
     *
     * @return  Kirchbergerknorr_Importer_Model_Reader_Interface
     */
    public function setData(&$data);

    /**
     * reads data from source
     *
     * @return  Kirchbergerknorr_Importer_Model_Reader_Interface            for chaining
     */
    public function read();

    /**
     * set logger
     *
     * @param   Kirchbergerknorr_Importer_Helper_Logger             $logger set logger
     *
     * @return  Kirchbergerknorr_Importer_Model_Reader_Interface            for chaining
     */
    public function setLogger($logger);

    /**
     * get logger
     *
     * @return  Kirchbergerknorr_Importer_Helper_Logger                     logger
     */
    public function getLogger();
}