<?php
/**
 * @category    Kirchbergerknorr
 * @package     Kirchbergerknorr/Importer/Model
 * @subpackage  Decorator
 * @author      Nick Dilßner <nick.dilssner@kirchbergerknorr.de>
 * @copyright   Copyright (c) 2016 kirchbergerknorr GmbH (http://www.kirchbergerknorr.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
abstract class Kirchbergerknorr_Importer_Model_Decorator implements Kirchbergerknorr_Importer_Model_Decorator_Interface
{
    /**
     * @var mixed   data to decorate
     */
    protected $data;

    /**
     * @var string  name for writer
     */
    protected $name;

    // TODO: use TRAITS
    /**
     * Kirchbergerknorr_Importer_Model_Decorator constructor.
     *
     * @param   mixed[]     $arguments  constructor arguments
     * @param   string[]    $keys       (optional) additional keys to check in arguments
     *
     * @throws  Kirchbergerknorr_Importer_Model_Decorator_Exception_InvalidArgumentException    if empty name in arguments
     *
     * @typedef Arguments
     * @var string  name    name for which decorator is used
     */
    public function __construct($arguments, $keys = array())
    {
        if (empty($arguments['name'])) {
            throw new Kirchbergerknorr_Importer_Model_Decorator_Exception_InvalidArgumentException('Mapping Name Missing');
        }
        foreach (array_merge($keys, array('name')) as $config) {
            if (isset($arguments[$config])) {
                $this->$config = $arguments[$config];
            }
        }
    }

    /**
     * get name for which the decorator is for
     *
     * @return  string  name for writer
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * set decorator decorate
     *
     * @param   mixed                                       $data   data to decorate
     *
     * @return  Kirchbergerknorr_Importer_Model_Decorator           for chaining
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }
}