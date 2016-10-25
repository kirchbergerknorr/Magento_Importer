<?php
/**
 * @category    Kirchbergerknorr
 * @package     Kirchbergerknorr/Importer/Model
 * @subpackage  Worker
 * @author      Nick Dilßner <nick.dilssner@kirchbergerknorr.de>
 * @copyright   Copyright (c) 2016 kirchbergerknorr GmbH (http://www.kirchbergerknorr.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Kirchbergerknorr_Importer_Model_Worker
{
    /**
     * @var Kirchbergerknorr_Importer_Model_Config worker config
     */
    protected $config;

    /**
     * @var Kirchbergerknorr_Importer_Model_Reader  reader
     */
    protected $reader;

    /**
     * @var Kirchbergerknorr_Importer_Model_Writer  writer
     */
    protected $writer;

    /**
     * @var mixed[]                                 reader / writer mapping
     */
    protected $mapping;

    /**
     * @var mixed[]                                 filters for writer
     */
    protected $filters;

    /**
     * @var Kirchbergerknorr_Importer_Helper_Logger logger
     */
    protected $logger;

    /**
     * @var bool    if set writer overrides fields with empty value
     */
    protected $override = false;

    /**
     * @var Kirchbergerknorr_Importer_Model_Decorator[] decorator cache for decorators which are configured as array
     */
    protected $decorators = array();
    
    /**
     * Kirchbergerknorr_Importer_Model_Worker constructor.
     *
     * @param   mixed[] $config
     *
     * @typedef Config
     * @var {string}                            name            importer name for loging
     * @var {Writer}                            writer          writer config
     * @var {Reader}                            reader          reader config
     * @var {bool}                              override        if set override with empty values if reader value is empty
     * @var {string[]}                          filters         filter mapping
     * @var {mixed[]|Decorator[]|Callback[]}    mapping         mapping as key = reader, value = writer
     *
     * @typedef {array}     Writer
     * @var {bool}                              update          set to update filtered model
     * @var {bool}                              create          set to create new models if no one was found with filter
     * @var {bool}                              delete          (not implemented yet) set to delete filtered model
     * @var {mixed[]}                           defaults        defaults which will always set to model
     *
     * @typedef {array}     Writer.Product
     * @var {string[]}                          defaultStock    default values for stock item after save
     *
     * @typedef {array}     Reader
     * @var {string}                            copy            path to copy data after read
     * @var {string}                            source          path / url where data is
     *
     * @typedef {array}     Reader.Csv
     * @var {string}                            delimiter       csv column delimiter
     * @var {string}                            enclosure       csv field enclosure -> frame
     * @var {string}                            escape          csv escaping char
     *
     * @typedef {array}     Decorator
     * @var {string}                            name            name for which decorator is used
     *
     * @typedef {array}     Decorator.HtmlList
     * @var {string}                            delimiter       csv column delimiter
     * @var {string}                            enclosure       csv field enclosure -> frame
     * @var {string}                            escape          csv escaping char
     *
     * @typedef {function}  Callback
     * @var {mixed}                             value           value from reader
     * @var {WriterObject}                      writer          writer
     * @var {ReaderObject}                      reader          reader
     */
    public function __construct($config)
    {
        $this->config = Mage::getModel('kirchbergerknorr_importer/config', $config);

        // required
        $this->init(array('reader', 'writer', 'mapping', 'filters'), true);
        // TODO: think about config position ... override is a writer option, but worker needs it to prevent useless code-runs
        // optional
        $this->init(array('override'));
        
        // inject logger
        $name = $this->config->get('name');
        foreach (array('reader', 'writer') as $worker) {
            if(!$this->$worker->getLogger()) {
                $this->$worker->setLogger(Mage::helper('kirchbergerknorr_importer/logger', array(
                    'name'  => $name,
                    'type'  => $worker,
                )));
            }
        }

        // create own logger
        $this->setLogger(Mage::helper('kirchbergerknorr_importer/logger', array(
            'name'  => $name,
            'type'  => 'worker'
        )));
    }

    /**
     * initialize worker from config for given keys
     *
     * @param   string[]    $keys
     * @param   bool        $required
     * @return  void
     *
     * @throws  \InvalidArgumentException if required key is missing
     */
    protected function init($keys, $required = false)
    {
        // TODO: think about own WorkerExceptions
        foreach ($keys as $key) {
            $value = $this->config->dynamic($key);
            if ($value !== null) {
                $this->$key = $value;
            } elseif ($required) {
                throw new InvalidArgumentException("$key is missing");
            }
        }
    }
    
    // TODO: validate if filter model is better then this
    // TODO: validate if this is enough, that filter is only first lvl or if recursion is needed
    /**
     * set filter to writer with values from reader
     *
     * @param   string[]                                            $filters    configured filters
     * @param   Kirchbergerknorr_Importer_Model_Reader_Interface    $reader     reader
     * @param   Kirchbergerknorr_Importer_Model_Writer_Interface    $writer     writer
     * @return  Kirchbergerknorr_Importer_Model_Worker                          for chaining
     */
    protected function setFilters($filters, Kirchbergerknorr_Importer_Model_Reader_Interface $reader, Kirchbergerknorr_Importer_Model_Writer_Interface $writer)
    {
        foreach ($filters as $read => $write) {
            $writer->addFilter($write, $reader->$read);
        }
        $writer->setDefaults();
        return $this;
    }

    /**
     * do the job
     *
     * @throws  Kirchbergerknorr_Importer_Model_Reader_Exception_UnexpectedValueException   if source cause an exception
     */
    public function run()
    {
        try {
            $this->getLogger()->log('START');
            $this->reader->read();

            // same as do-while-loop, but without possible errors
            // Explained: for (startup(); while(); beforeNextIteration()) { ... }
            for ($this->reader->rewind(); $this->reader->valid(); $this->reader->next()) {

                $writer = clone $this->writer;
                $this->setFilters($this->filters, $this->reader, $writer);

                // run only if model was found or create is active
                if ($writer->isWriteAble()) {
                    $this->read($this->mapping, $this->reader, $writer);

                    $writer->save();
                }
            }
            $this->getLogger()->log('END');
        } catch (Exception $exception) {
            $this->getLogger()->exception($exception);
            exit;
        }
    }

    /**
     * read mapping for current interval
     *
     * @param   mixed[]                                     $mapping    configured field mapping
     * @param   Kirchbergerknorr_Importer_Model_Reader      $reader     source wrapper
     * @param   Kirchbergerknorr_Importer_Model_Writer      $writer     magento model wrapper
     * @param   string                                      $key        (optional) combined config key to get sub-config in recursion
     * @return  void
     */
    public function read($mapping, $reader, $writer, $key = 'mapping')
    {
        foreach ($mapping as $read => $write) {
            $value = null;
            if (($value = $reader->$read) || $this->override) {
                switch (gettype($write)) {
                    case 'string':
                        $writer->$write = $value;
                        break;
                    case 'object':
                        if ($write instanceof Kirchbergerknorr_Importer_Model_Decorator_Interface) {
                            $writer->{$write->getName()} = $write->setData($value)->__toString();
                        }
                        if (is_callable($write)) {
                            // Callback must handle data itself
                            /**
                             * @typedef Callback
                             * @var mixed                                   value   value   value from source
                             * @var Kirchbergerknorr_Importer_Model_Writer  writer  $writer writer to set data
                             * @var Kirchbergerknorr_Importer_Model_Reader  reader  $reader reader for additional things to do
                             */
                            $write($reader->$read, $writer, $reader);
                        }
                        break;
                    case 'array':
                        $key = "$key/$read";
                        if (isset($write['decorator'])) {
                            // if magic config get a model it was a decorator
                            $decorator = (isset($this->decorators[$key])) ? $this->decorators[$key] : $this->decorators[$key] = $this->config->get("$key/decorator");
                            if ($decorator && $decorator instanceof Kirchbergerknorr_Importer_Model_Decorator_Interface) {
                                $writer->{$decorator->getName()} = $decorator->setData($value)->__toString();
                            }
                        } else if ($reader->hasChildren()) {
                            // recursion
                            $this->read($write, $reader->getChildren(), $writer, $key);
                        }
                        break;
                    default:
                        // TODO: validate
                        break;
                }
            }
            $this->getLogger()
                ->log("read - key: '$key' type: '" . gettype($write) . "' value: " . $value)
                ->debug(array('read', gettype($write), $value));
        }
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
        $this->logger = $logger;
        return $this;
    }

    /**
     * get logger
     *
     * @return  Kirchbergerknorr_Importer_Helper_Logger logger
     */
    public function getLogger()
    {
        return $this->logger;
    }
    // LOGGER>
}