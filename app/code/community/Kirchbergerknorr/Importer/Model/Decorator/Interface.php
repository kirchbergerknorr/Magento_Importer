<?php
/**
 * @category    Kirchbergerknorr
 * @package     Kirchbergerknorr/Importer/Model/Decorator
 * @subpackage  Interface
 * @author      Nick Dilßner <nick.dilssner@kirchbergerknorr.de>
 * @copyright   Copyright (c) 2016 kirchbergerknorr GmbH (http://www.kirchbergerknorr.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
interface Kirchbergerknorr_Importer_Model_Decorator_Interface
{
    /**
     * set data to decorator
     *
     * @param   mixed                                               $data
     *
     * @return  Kirchbergerknorr_Importer_Model_Decorator_Interface         for chaining
     */
    public function setData($data);

    /**
     * get name of property key which decorator represent
     *
     * @return  string  name of property key which decorator represent
     */
    public function getName();

    /**
     * decorate
     *
     * @return  string  decorated data
     */
    public function __toString();
}