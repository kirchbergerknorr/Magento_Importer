<?php
/**
 * @category    Kirchbergerknorr
 * @package     Kirchbergerknorr/Importer/Model/Decorator
 * @subpackage  HtmlList
 * @author      Nick Dilßner <nick.dilssner@kirchbergerknorr.de>
 * @copyright   Copyright (c) 2016 kirchbergerknorr GmbH (http://www.kirchbergerknorr.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Kirchbergerknorr_Importer_Model_Decorator_HtmlList extends Kirchbergerknorr_Importer_Model_Decorator
{
    /**
     * @var bool    decorate empty list
     */
    public $empty = false;

    // TODO: implement
    /**
     * @var string  merge list - leafs will be merged to one
     */
    public $merge = '';

    /**
     * Kirchbergerknorr_Importer_Model_Decorator_HtmlList constructor.
     *
     * @inheritdoc
     * @var string  merge   (not implement) if set merge last data notes (leafs) to one '<li>' and use value as separator
     * @var bool    empty   if set empty 'li's will be created if data value is empty
     *
     * @param   mixed[] $arguments
     */
    public function __construct($arguments)
    {
        parent::__construct($arguments);

        // TODO: use PHPTrap for arguments extraction
        foreach (array('empty', 'merge') as $key) {
            if (isset($arguments[$key])) {
                $this->$key = $arguments[$key];
            }
        }
    }

    // TODO: remove empty li values through construct arguments
    /**
     * decorate values
     *
     * @return  string  decorated value
     */
    public function __toString()
    {
        // TODO: optimize - this is currently for a string[][] array
        $return = '';
        if ($data = current($this->data)) {
            $return .= $this->decorate($data);
        }
        return $return;
    }

    // TODO: validate str-length
    // TODO: implement merged
    /**
     * decorate values
     *
     * @param   mixed   $data   data to be decorated
     *
     * @return  string          decorated data
     */
    protected function decorate($data)
    {
        if($this->validate($data)) {
            if (is_array($data)) {
                // TODO: find a better solution for [null]
                if ($this->validate(current($data))) {
                    $return = '<ul>';
                    foreach ($data as $item) {
                        $return .= ($this->validate($item, true)) ? '<li>' . $this->decorate($item) . '</li>' : '';
                    }
                    return $return . '</ul>';
                }
            } else {
                // TODO: check if useful
                return ($this->validate($data)) ? $data : '';
            }
        }
        return '';
    }

    // TODO: count str-length
    /**
     * validates if data is append-able
     *
     * @param   mixed   $data       data that will be set
     * @param   bool    $recursive  look recursive through given data
     *
     * @return  bool                true if valid
     */
    protected function validate($data, $recursive = false)
    {
        if ($this->empty) {
            return true;
        }
        if ($recursive && is_array($data)) {
            foreach ($data as $value) {
                if (is_array($value)) {
                    return $this->validate($value, true);
                } elseif ($value) {
                    return true;
                }
            }
            return false;
        }
        return (!empty($data));
    }
}