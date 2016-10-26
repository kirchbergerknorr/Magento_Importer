<?php
/**
 * @category    Kirchbergerknorr
 * @package     Kirchbergerknorr/Importer/Model
 * @subpackage  Reader
 * @author      Nick Dilßner <nick.dilssner@kirchbergerknorr.de>
 * @copyright   Copyright (c) 2016 kirchbergerknorr GmbH (http://www.kirchbergerknorr.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
abstract class Kirchbergerknorr_Importer_Model_Reader implements Kirchbergerknorr_Importer_Model_Reader_Interface, RecursiveIterator
{
    /**
     * @var string[]    content
     */
    protected $_data;

    /**
     * @var Kirchbergerknorr_Importer_Helper_Logger logger
     */
    protected $_logger;

    /**
     * @var string  source - path or url
     */
    protected $_source;

    /**
     * @var string  if set data will be copied to that path after read
     */
    protected $_copy;

    // TODO: use TRAITS
    /**
     * Kirchbergerknorr_Importer_Model_Reader constructor.
     *
     * @param   mixed[]     $arguments  (optional) constructor arguments
     * @param   string[]    $keys       (optional) additional keys which will be searched in arguments
     *
     * @typedef Arguments
     * @var string  copy    path to copy data after read
     * @var string  source  path / url where data is
     */
    public function __construct($arguments = array(), $keys = array())
    {
        foreach (array_merge($keys, array('copy', 'source')) as $config) {
            if (isset($arguments[$config])) {
                $this->{'_' . $config} = $arguments[$config];
            }
        }
    }

    /**
     * copy given data to location
     *
     * @param   mixed                                   $data   data to copy
     *
     * @return  Kirchbergerknorr_Importer_Model_Reader          for chaining
     */
    protected function copy($data)
    {
        // TODO: implement like \Kirchbergerknorr_Import_Model_Config::dynamic()
        if (!empty($this->_copy)) {
            $date = new DateTime('now');
            $path = Mage::getBaseDir() . DS . ltrim($this->_copy, DS) . 'imported_' . $date->format('Ymd') . '.log';
            file_put_contents(
                $path,
                $data
            );

        }
        return $this;
    }

    // <INTERFACE
    /**
     * get value from data for given key
     *
     * @param   string  $key    key to get from data
     *
     * @return  mixed           value
     */
    public function __get($key)
    {
        $current = $this->current();
        return ((isset($current[$key])) ? $current[$key] : null);
    }

    /**
     * set data
     * 
     * @param   mixed                                   $data   data to set
     *
     * @return  Kirchbergerknorr_Importer_Model_Reader
     */
    public function setData(&$data)
    {
        $this->_data = $data;
        $this->getLogger()->debug(array('setData', $data));
        return $this;
    }

    /**
     * reads data from source
     *
     * @return Kirchbergerknorr_Importer_Model_Reader                                       for chaining
     *
     * @throws Kirchbergerknorr_Importer_Model_Reader_Exception_InvalidArgumentException    if no source is set
     * @throws Kirchbergerknorr_Importer_Model_Reader_Exception_UnexpectedValueException    if data is not read-able
     */
    public function read()
    {
        if (!$this->_source) {
            throw new Kirchbergerknorr_Importer_Model_Reader_Exception_InvalidArgumentException('source is missing');
        }
        if (($data = file_get_contents($this->_source)) && $data !== false) {
            $this->getLogger()->log('read data - success');
            return $this
                ->copy($data)
                ->setData($data);
        }
        throw new Kirchbergerknorr_Importer_Model_Reader_Exception_UnexpectedValueException('while reading source');
    }
    // INTERFACE>

    // <ITERATOR
    /**
     * Returns the current element.
     *
     * @return  mixed   value of current pointer
     */
    public function current()
    {
        return current($this->_data);
    }

    /**
     * Moves the current position to the next element.
     *
     * @return  void
     */
    public function next()
    {
        next($this->_data);
    }

    /**
     * Returns the key of the current element.
     *
     * @return  string|int  key of current
     */
    public function key()
    {
        return key($this->_data);
    }

    /**
     * This method is called after Iterator::rewind() and Iterator::next() to check if the current position is valid.
     *
     * @return  bool    true if valid
     */
    public function valid()
    {
        return (key_exists($this->key(), $this->_data));
    }

    /**
     * Rewinds back to the first element of the Iterator.
     *
     * @return  void
     */
    public function rewind()
    {
        reset($this->_data);
    }
    // ITERATOR>

    // <RECURSIVE-ITERATOR
    /**
     * Returns if an iterator can be created fot the current entry.
     *
     * @return  bool    true if iterator is creatable
     */
    public function hasChildren()
    {
        return (is_array($this->current()));
    }

    /**
     * Returns an iterator for the current entry.
     *
     * @return  Kirchbergerknorr_Importer_Model_Reader  iterator of current position
     */
    public function getChildren()
    {
        // late static binding
        $class = get_called_class();
        /** @var Kirchbergerknorr_Importer_Model_Reader $class */
        $class = new $class();
        return $class->setData($this->current());
    }
    // RECURSIVE-ITERATOR>

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