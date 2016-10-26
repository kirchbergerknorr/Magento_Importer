<?php
/**
 * @category    Kirchbergerknorr
 * @package     Kirchbergerknorr/Importer/Model
 * @subpackage  Writer
 * @author      Nick Dilßner <nick.dilssner@kirchbergerknorr.de>
 * @copyright   Copyright (c) 2016 kirchbergerknorr GmbH (http://www.kirchbergerknorr.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
abstract class Kirchbergerknorr_Importer_Model_Writer implements Kirchbergerknorr_Importer_Model_Writer_Interface
{
    /**
     * @var Mage_Catalog_Model_Abstract dummy model to get collection from
     */
    protected $_model;

    /**
     * @var Kirchbergerknorr_Importer_Helper_Logger logger
     */
    protected $_logger;

    /**
     * @var Mage_Eav_Model_Entity_Collection_Abstract   collection for filter
     */
    protected $_collection;

    /**
     * @var Mage_Catalog_Model_Abstract filtered model
     */
    protected $_filtered;

    /**
     * @var mixed[] default values for model
     */
    protected $_defaults = array();

    /**
     * @var bool    if true update filtered model
     */
    protected $_update = true;

    /**
     * @var bool    if true create model if no filtered was found
     */
    protected $_create = true;

    // TODO: implement
    /**
     * @var bool    if set delete filtered model
     */
    protected $_delete = false;

    // TODO: use TRAITS
    /**
     * Kirchbergerknorr_Importer_Model_Writer constructor.
     *
     * @param array $arguments
     * @param array $keys
     *
     * @typedef Arguments
     * @var bool    update      set to update filtered model
     * @var bool    create      set to create new models if no one was found with filter
     * @var bool    delete      (not implemented yet) set to delete filtered model
     * @var mixed[] defaults    defaults which will always set to model
     */
    public function __construct($arguments = array(), $keys = array())
    {
        foreach (array_merge($keys, array('update', 'create', 'delete', 'defaults')) as $config) {
            if (isset($arguments[$config])) {
                $this->{'_' . $config} = $arguments[$config];
            }
        }
        $this->init();
    }

    /**
     * init model
     *
     * @return  Kirchbergerknorr_Importer_Model_Writer  for chaining
     */
    protected function init()
    {
        // reset filtered
        $this->_filtered = null;

        $this->_collection = clone $this->_model->getCollection();
        return $this;
    }

    /**
     * add filter to model
     *
     * @param   string                                  $filter filter attribute name
     * @param   mixed                                   $value  value for filter
     * @return  Kirchbergerknorr_Importer_Model_Writer          for chaining
     */
    public function addFilter($filter, $value)
    {
        $this->getLogger()->log("addFilter - filter: '$filter' value: '$value'");
        $this->_collection->addAttributeToFilter($filter, $value);
        return $this;
    }
    
    // TODO: implement log create/update/...
    /**
     * save set data to model
     * 
     * @return  Kirchbergerknorr_Importer_Model_Writer
     */
    public function save()
    {
        if ($this->isWriteAble()) {
            $model = $this->getFiltered();
            $model->save();

            $this->getLogger()
                ->log('save model ID: ' . $model->getId())
                ->debug(array('save', $model));

            $this->afterSave();
        }
    }

    /**
     * magic clone - should be called if new / next model is needed
     *
     * @return  void
     */
    public function __clone()
    {
        $this->init();
    }

    /**
     * set given value to model for given name
     *
     * @param   string  $name   key
     * @param   mixed   $value  value to set
     */
    public function __set($name, $value)
    {
        $this->getLogger()->debug(array('__set', $name, $value));
        $this->getFiltered()->setData($name, $value);
    }

    /**
     * validates if model is write-able or not
     *
     * @return  bool    true if write-able
     */
    public function isWriteAble()
    {
        $hasId = (!!$this->getFiltered()->getId());
        return (
            (!$hasId && $this->_create)
            || ($hasId && $this->_update)
        );
    }

    /**
     * get filtered model
     *
     * @return  Mage_Core_Model_Abstract    filtered model -> needs to be validated if id is set
     */
    public function getFiltered()
    {
        if ($this->_filtered === null) {
            $this->_filtered = $this->_collection->getFirstItem();

            if ($this->_filtered->getId()) {
                $this->getLogger()->log('getFiltered - found match ID: ' . $this->_filtered->getId());
            } else {
                $this->getLogger()->log('getFiltered - no match');
            }
        }
        return $this->_filtered;
    }

    /**
     * set defaults to model
     * 
     * @return  Kirchbergerknorr_Importer_Model_Writer  for chaining
     */
    public function setDefaults()
    {
        foreach ($this->_defaults as $key => $value) {
            // set through magic setter
            $this->$key = $value;
        }
        return $this;
    }

    /**
     * do stuff after save model
     *
     * @return  Kirchbergerknorr_Importer_Model_Writer  for chaining
     */
    protected function afterSave()
    {
        // DUMMY - do some stuff after save in Model
        return $this;
    }
    
    // <LOGGER
    /**
     * set logger for log messages
     *
     * @param   Kirchbergerknorr_Importer_Helper_Logger $logger logger
     * @return  Kirchbergerknorr_Importer_Model_Writer          for chaining
     */
    public function setLogger($logger)
    {
        $this->_logger = $logger;
        return $this;
    }

    /**
     * get logger
     *
     * @return  Kirchbergerknorr_Importer_Helper_Logger logger
     */
    public function getLogger()
    {
        return $this->_logger;
    }
    // LOGGER>
}