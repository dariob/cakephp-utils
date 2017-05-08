<?php
namespace Qobo\Utils\Test\TestCase\ModuleConfig\Parser\Ini;

use Cake\Core\Configure;
use PHPUnit_Framework_TestCase;
use Qobo\Utils\ModuleConfig\Parser\Ini\ConfigParser;

class ConfigParserTest extends PHPUnit_Framework_TestCase
{
    protected $parser;
    protected $dataDir;

    protected function setUp()
    {
        $this->parser = new ConfigParser();
        $this->dataDir = dirname(dirname(dirname(dirname(__DIR__)))) . DS . 'data' . DS . 'Modules' . DS;
        Configure::write('CsvMigrations.modules.path', $this->dataDir);
    }

    public function testParse()
    {
        $file = $this->dataDir . 'Foo' . DS . 'config' . DS . 'config.ini';
        $result = $this->parser->parse($file);

        $this->assertTrue(is_object($result), "Parser returned a non-object");

        // Convert object to array recursively
        $result = json_decode(json_encode($result), true);

        $this->assertFalse(empty($result), "Parser returned empty result");

        $this->assertFalse(empty($result['table']), "Parser missed 'table' section");
        $this->assertFalse(empty($result['table']['display_field']), "Parser missed 'display_field' key");
        $this->assertEquals('name', $result['table']['display_field'], "Parser misinterpreted 'display_field' value");
    }

    public function testParseWithDefaults()
    {
        $file = $this->dataDir . 'Foo' . DS . 'config' . DS . 'config_no_table.ini';
        $result = $this->parser->parse($file);

        $this->assertTrue(is_object($result), "Parser returned a non-object");

        // Convert object to array recursively
        $result = json_decode(json_encode($result), true);

        $this->assertFalse(empty($result), "Parser returned empty result");

        $this->assertFalse(empty($result['table']), "Parser missed 'table' section");
        $this->assertFalse(empty($result['table']['icon']), "Parser missed 'icon' default key");
    }

    public function testParseTestingArrays()
    {
        $file = $this->dataDir . 'Foo' . DS . 'config' . DS . 'array_in_config.ini';
        $result = $this->parser->parse($file);

        $this->assertTrue(is_object($result), "Parser returned a non-object");

        // Convert object to array recursively
        $result = json_decode(json_encode($result), true);

        $this->assertArrayHasKey('associations', $result, "No associations found in the table config");
        $this->assertArrayHasKey('association_labels', $result['associations'], "No associations found in the table config");
        $this->assertTrue(is_array($result['associations']['association_labels']), "Associations label is not an array");
    }

    public function testParseMissing()
    {
        $file = $this->dataDir . 'Foo' . DS . 'config' . DS . 'this_file_does_not_exist.ini';
        $result = $this->parser->parse($file);

        $this->assertTrue(is_object($result), "Parser returned a non-object");

        // Make sure warnings are not empty
        $warnings = $this->parser->getWarnings();
        $this->assertTrue(is_array($warnings), "Warnings is not an array");
        $this->assertFalse(empty($warnings), "Warnings are empty");

        // Convert object to array recursively
        $result = json_decode(json_encode($result), true);

        $this->assertFalse(empty($result['table']), "Parser missed 'table' section");
        $this->assertFalse(empty($result['table']['icon']), "Parser missed 'icon' default key");
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testParseBad()
    {
        $file = $this->dataDir . 'Foo' . DS . 'config' . DS . 'config_bad.ini';
        $result = $this->parser->parse($file);
    }
}