<?php
/**
 * @category    Kirchbergerknorr
 * @package     Kirchbergerknorr/Importer/Model/Writer
 * @subpackage  Interface
 * @author      Nick Dilßner <nick.dilssner@kirchbergerknorr.de>
 * @copyright   Copyright (c) 2016 kirchbergerknorr GmbH (http://www.kirchbergerknorr.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
interface Kirchbergerknorr_Importer_Model_Writer_Interface
{
    /**
     * add filter to model
     *
     * @param   string                                              $filter filter attribute name
     * @param   mixed                                               $value  value for filter
     * @return  Kirchbergerknorr_Importer_Model_Writer_Interface            for chaining
     */
    public function addFilter($filter, $value);

    /**
     * save data to model
     *
     * @return  Kirchbergerknorr_Importer_Model_Writer_Interface            for chaining
     */
    public function save();

    /**
     * magic clone - should be called if new / next model is needed
     *
     * @return  void
     */
    public function __clone();

    /**
     * magic setter
     *
     * @param   string  $name   key
     * @param   mixed   $value  value to set
     *
     * @return  void
     */
    public function __set($name, $value);

    /**
     * set defaults to model
     *
     * @return  Kirchbergerknorr_Importer_Model_Writer_Interface            for chaining
     */
    public function setDefaults();

    /**
     * set logger
     *
     * @param   Kirchbergerknorr_Importer_Helper_Logger             $logger logger
     *
     * @return  Kirchbergerknorr_Importer_Model_Writer_Interface            for chaining
     */
    public function setLogger($logger);

    /**
     * get logger
     *
     * @return  Kirchbergerknorr_Importer_Helper_Logger                     logger
     */
    public function getLogger();
}