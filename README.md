# MagentoImporter

Import Data to Magento through powerful config.

## Features

### Writer Models

- Product

### Reader Models

- Xml
- Csv

### Decorators

- HtmlList

## ToDo's

- General
    - more Writer models
    - more Decorators
    - make config through backend possible
    - mapping filter as object
    - create Extractors -> like BBCodes/Wysiwyg

- Config
    - (MAYBE) other Logger possibility

- Writer
    - delete
    - filters like max_length

- Reader
    - read in buckets -> not every time possible, but build a wrapper for it
    - Extractors
        - implement

- Decorator
    - HtmlList
        - merge leafs to one li

- Worker
    - make model creation more generic for easier add of new models -> Model Injection

## Example

```
/** @var Kirchbergerknorr_Importer_Model_Worker $worker */
$worker = Mage::getModel('kirchbergerknorr_importer/worker', array(
    'name'      => 'my-name-for-logging-for-example',
    'writer'    => array(
        'model'     => 'Product',
        'arguments' => array(
            'update'    => true,
            'create'    => false,
            'defaults'  => array(
                'attribute_set_id'  => 4,
            ),
            // TODO: implement
            'filters'   => array(
                // DB varchar length
                'max_length'    => 65535,
            )
        )
    ),
    'reader'    => array(
        'model'     => 'Xml',
        'arguments' => array(
            'copy'      => 'var/log/import/mesat/',
            'source'    => 'http://my-url.de/import/import-YYYYMMDD.xml',
        )
    ),
    'override'  => false,
    'filters'   => array(
        'SKU'       => 'sku',
    ),
    'mapping'   => array(
        'PRODUCT_DESCRIPTION'   => 'description',
        'TECHNICAL_INFOS'       => array(
            // will create decorator model, like in 'PRODUCT_FEATURES'
            'decorator'             => array(
                'model'                 => 'HtmlList',
                'arguments'             => array(
                    // TODO: think about name 'mapping'
                    'name'  => 'technical_details',
                    'merge' => true,
                    'empty' => false
                )
            )
        ),
        'PRODUCT_FEATURES'      => Mage::getModel('kirchbergerknorr_importer/decorator_HtmlList', array(
            'name'  => 'features',
            'merge' => false,
            'empty' => true
        )),
        'SIZE_AND_WEIGHT'       => function($value, $writer, $reader) {
            if ($value && ($tmp = explode(';', $value))) {
                // set size from first value
                if (isset($tmp[0])) {
                    $writer->size = $tmp[0];
                }
                // set weight from second value
                if (isset($tmp[1])) {
                    $writer->weight = $tmp[1];
                }
            }
        }
    )
));

$worker->run();
```

## Config Doc

```
/**
 * in JSDoc form
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
```