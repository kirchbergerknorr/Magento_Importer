<?php
/**
 * @category    Kirchbergerknorr
 * @package     Kirchbergerknorr/Importer/Model/Reader
 * @subpackage  Csv
 * @author      Nick Dilßner <nick.dilssner@kirchbergerknorr.de>
 * @copyright   Copyright (c) 2016 kirchbergerknorr GmbH (http://www.kirchbergerknorr.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Kirchbergerknorr_Importer_Model_Reader_Csv extends Kirchbergerknorr_Importer_Model_Reader
{
    /**
     * @var string  csv column delimiter
     */
    protected $_delimiter = ",";

    /**
     * @var string  csv field enclosure -> frame
     */
    protected $_enclosure = '"';

    /**
     * @var string  csv escaping char
     */
    protected $_escape = "\\";

    /**
     * @inheritdoc
     * @var string  delimiter   csv column delimiter
     * @var string  enclosure   csv field enclosure -> frame
     * @var string  escape      csv escaping char
     */
    public function __construct($arguments = array())
    {
        parent::__construct($arguments, array('delimiter', 'enclosure', 'escape'));
    }

    /**
     * @inheritdoc
     */
    public function read()
    {
        if (!$this->_source) {
            throw new Kirchbergerknorr_Importer_Model_Reader_Exception_InvalidArgumentException('source is missing');
        }
        ini_set('auto_detect_line_endings', TRUE);
        // TODO: create a copy for external csv's -> also this way is not optimized, maybe override iterator methods
        $data = array();
        if ($handle = fopen($this->_source, 'r')) {
            while ($row = fgetcsv($handle, 0, $this->_delimiter, $this->_enclosure, $this->_escape)) {
                $data[] = $row;
            }
            // TODO: copy
            return $this
                ->setData($data);
        }
        throw new Kirchbergerknorr_Importer_Model_Reader_Exception_UnexpectedValueException('while reading source');
    }
}