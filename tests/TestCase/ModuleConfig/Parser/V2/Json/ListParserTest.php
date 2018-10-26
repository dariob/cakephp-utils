<?php
namespace Qobo\Utils\Test\TestCase\ModuleConfig\Parser\V2\Json;

use Cake\Core\Configure;
use PHPUnit\Framework\TestCase;
use Qobo\Utils\ModuleConfig\Parser\V2\Json\ListParser;

class ListParserTest extends TestCase
{
    protected $parser;
    protected $dataDir;

    public function setUp()
    {
        $this->parser = new ListParser();
        $this->dataDir = TESTS . 'data' . DS . 'Modules';

        Configure::write('CsvMigrations.modules.path', $this->dataDir);
        Configure::write('ModuleConfig.classMapVersion', 'V2');
    }

    public function tearDown()
    {
        unset($this->parser);
        unset($this->dataDir);
    }

    public function testParse()
    {
        $file = $this->dataDir . DS . 'Foo' . DS . 'lists' . DS . 'local_genders.json';
        $result = $this->parser->parse($file);

        $resultArray = json_decode(json_encode($result), true);

        $this->assertNotEmpty($result);
        $this->assertArrayHasKey('items', $resultArray);
        $this->assertEquals($resultArray['items']['m']['label'], 'M - Male');
    }

    public function testFilter()
    {
        $file = $this->dataDir . DS . 'Foo' . DS . 'lists' . DS . 'local_genders.json';
        $result = $this->parser->parse($file, ['filter' => true]);

        $resultArray = json_decode(json_encode($result), true);
        $this->assertTrue(!in_array('foo', array_keys($resultArray['items'])));
    }

    public function testFlatten()
    {
        $file = $this->dataDir . DS . 'Foo' . DS . 'lists' . DS . 'local_genders.json';
        $result = $this->parser->parse($file, ['filter' => true, 'flatten' => true]);

        $resultArray = json_decode(json_encode($result), true);

        $this->assertTrue(in_array('bar.bar_one', array_keys($resultArray['items'])));
        $this->assertTrue(in_array('bar.bar_two', array_keys($resultArray['items'])));
    }
}