<?php
/**
 * @category    Kirchbergerknorr
 * @package     Kirchbergerknorr/Importer/Model
 * @subpackage  Factory
 * @author      Nick Dilßner <nick.dilssner@kirchbergerknorr.de>
 * @copyright   Copyright (c) 2016 kirchbergerknorr GmbH (http://www.kirchbergerknorr.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Kirchbergerknorr_Importer_Model_Factory
{
    const IMPORTER_NAMESPACE = 'kirchbergerknorr_importer/';

    /**
     * get reader model
     *
     * @param   string                                  $name   name of specific model
     * @param   mixed                                   $data   model data
     *
     * @return  Kirchbergerknorr_Importer_Model_Reader          reader model
     */
    public function getReader($name, $data)
    {
        return $this->get('reader', $name, $data);
    }

    /**
     * get writer model
     *
     * @param   string                                  $name   name of specific model
     * @param   mixed                                   $data   model data
     *
     * @return  Kirchbergerknorr_Importer_Model_Writer          writer model
     */
    public function getWriter($name, $data)
    {
        return $this->get('writer', $name, $data);
    }

    /**
     * get model
     *
     * @param   string      $type       model type
     * @param   string      $name       name of specific model
     * @param   mixed       $arguments  (optional) model arguments
     *
     * @return  false|mixed
     */
    public function get($type, $name, $arguments = array())
    {
        return Mage::getModel(self::IMPORTER_NAMESPACE . $type . '_' . $name, $arguments);
    }
}