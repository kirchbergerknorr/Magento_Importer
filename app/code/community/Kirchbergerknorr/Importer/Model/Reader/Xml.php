<?php
/**
 * @category    Kirchbergerknorr
 * @package     Kirchbergerknorr/Importer/Model/Reader
 * @subpackage  Xml
 * @author      Nick Dilßner <nick.dilssner@kirchbergerknorr.de>
 * @copyright   Copyright (c) 2016 kirchbergerknorr GmbH (http://www.kirchbergerknorr.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Kirchbergerknorr_Importer_Model_Reader_Xml extends Kirchbergerknorr_Importer_Model_Reader
{
    /**
     * @var SimpleXMLIterator   content
     */
    protected $_data;

    /**
     * extract content recursive from data
     *
     * @param   SimpleXMLIterator   $data   iterator with content
     *
     * @return  mixed[]|null
     */
    private function extract(SimpleXMLIterator $data)
    {
        if ($data->count() > 0) {
            if ($data->hasChildren()) {
                // extract recursive
                $return = array();
                foreach ($data->getChildren() as $key => $child) {
                    // reset pointer, to make multiple usage possible
                    $child->rewind();
                    // no key possible, because
                    $return[$key][] = $this->extract($child);
                }
                return $return;
            } else {
                if ($data->count() == 1) {
                    return $data->__toString();
                } else {
                    // extract array data
                    $return = array();
                    foreach ($data as $key => $unusable) {
                        $return[$key] = $this->extract($data->$key);
                    }
                    return $return;
                }
            }
        }
        return null;
    }

    // <INTERFACE
    // TODO: implement a __toArray() method if needed -> recursive
    /**
     * @inheritdoc
     */
    public function __get($key)
    {
        /** @var SimpleXMLIterator $data */
        $data = $this->_data->current()->$key;
        // prevent return an Iterator
//        return ($data->count() == 1) ? $data->__toString() : null;
        // reset pointer, to make multiple usage possible
        $data->rewind();
        return $this->extract($data);
    }

    /**
     * @inheritdoc
     *
     * @return  Kirchbergerknorr_Importer_Model_Reader_Xml      for chaining
     */
    public function setData(&$data)
    {
        return parent::setData(($data instanceof SimpleXMLIterator) ? $data : new SimpleXMLIterator($data));
    }
    // INTERFACE>

    // TODO: check if a good solution or conversion is needed
    // <ITERATOR
    /**
     * @inheritdoc
     */
    public function current()
    {
        return new self($this->_data->current());
    }

    /**
     * @inheritdoc
     */
    public function next()
    {
        $this->_data->next();
    }

    /**
     * @inheritdoc
     */
    public function key()
    {
        return $this->_data->key();
    }

    /**
     * @inheritdoc
     */
    public function valid()
    {
        return $this->_data->valid();
    }

    /**
     * @inheritdoc
     */
    public function rewind()
    {
        $this->_data->rewind();
    }
    // ITERATOR>

    // <RECURSIVE-ITERATOR
    /**
     * @inheritdoc
     */
    public function hasChildren()
    {
        return $this->_data->hasChildren();
    }

    /**
     * @inheritdoc
     *
     * @return  Kirchbergerknorr_Importer_Model_Reader
     */
    public function getChildren()
    {
        $class = new self();
        return $class->setData($this->_data->getChildren());
    }
    // RECURSIVE-ITERATOR>
}