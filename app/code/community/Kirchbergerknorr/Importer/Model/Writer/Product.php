<?php
/**
 * @category    Kirchbergerknorr
 * @package     Kirchbergerknorr/Importer/Model/Writer
 * @subpackage  Product
 * @author      Nick Dilßner <nick.dilssner@kirchbergerknorr.de>
 * @copyright   Copyright (c) 2016 kirchbergerknorr GmbH (http://www.kirchbergerknorr.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Kirchbergerknorr_Importer_Model_Writer_Product extends Kirchbergerknorr_Importer_Model_Writer
{
    /**
     * @var int[]   stock values from worker
     */
    protected $_stock = array();

    // TODO: validate defaults
    /**
     * @var int[]   default stock values will be merged with stock data
     */
    protected $_defaultsStock = array(
        'is_in_stock'                   => 0,
//        'stock_id'                      => 1,
        'manage_stock'                  => 0,
        'notify_stock_qty'              => 0,
        'use_config_notify_stock_qty'   => 0,
        'use_config_qty_increments'     => 0,
        'use_config_enable_qty_inc'     => 0,
        'use_config_min_qty'            => 0,
        'use_config_backorders'         => 0,
        'use_config_manage_stock'       => 0,
        'min_sale_qty'                  => 0,
        'use_config_min_sale_qty'       => 0,
        'max_sale_qty'                  => 0,
        'use_config_max_sale_qty'       => 0,
        'qty'                           => 0,
    );

    /**
     * @inheritdoc
     * @var string[]    defaultStock    default values for stock item after save
     */
    public function __construct($arguments = array())
    {
        $this->_model = Mage::getModel('catalog/product');
        parent::__construct($arguments, array('defaultsStock'));
    }

    /**
     * @inheritdoc
     */
    public function __clone()
    {
        parent::__clone();
        // reset stock
        $this->_stock = array();
    }

    /**
     * set data to model for given name
     *
     * @param   string  $name   key
     * @param   mixed   $value  value to set
     */
    public function __set($name, $value)
    {
        if (isset($this->_defaultsStock[$name])) {
            $this->_stock[$name] = $value;
        } else {
            parent::__set($name, $value);
        }
    }

    /**
     * @inheritdoc
     */
    protected function afterSave()
    {
        Mage::getModel('cataloginventory/stock_item')
            ->assignProduct($this->getFiltered())
            ->addData(array_merge($this->_defaultsStock, $this->_stock))
            ->save();
    }
}