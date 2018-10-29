<?php
namespace Qobo\Utils\Test\TestCase\ModuleConfig\PathFinder\V1;

use Cake\Core\Configure;
use PHPUnit\Framework\TestCase;
use Qobo\Utils\ModuleConfig\PathFinder\V1\ListPathFinder;

class ListPathFinderTest extends TestCase
{
    protected $pf;
    protected $dataDir;

    protected function setUp()
    {
        $this->pf = new ListPathFinder();
        $this->dataDir = dirname(dirname(dirname(dirname(__DIR__)))) . DS . 'data' . DS . 'Modules' . DS;
        Configure::write('CsvMigrations.modules.path', $this->dataDir);
        Configure::write('ModuleConfig.classMapVersion', 'V1');
    }

    public function testInterface(): void
    {
        $implementedInterfaces = array_keys(class_implements($this->pf));
        $this->assertTrue(in_array('Qobo\Utils\ModuleConfig\PathFinder\PathFinderInterface', $implementedInterfaces), "PathFinderInterface is not implemented");
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testFind(): void
    {
        $path = $this->pf->find('Foo');
    }

    public function testFindSimple(): void
    {
        $path = $this->pf->find(null, 'foo_statuses');
        $this->assertFalse(empty($path), "Path is empty [$path]");
        $this->assertTrue(is_string($path), "Path is not a string [$path]");
        $this->assertTrue(file_exists($path), "Path does not exist [$path]");
        $this->assertTrue(is_readable($path), "Path is not readable [$path]");
        $this->assertTrue(is_file($path), "Path is not a file [$path]");
    }

    public function testFindSimpleFull(): void
    {
        $path = $this->pf->find(null, 'foo_statuses.csv');
        $this->assertFalse(empty($path), "Path is empty [$path]");
        $this->assertTrue(is_string($path), "Path is not a string [$path]");
        $this->assertTrue(file_exists($path), "Path does not exist [$path]");
        $this->assertTrue(is_readable($path), "Path is not readable [$path]");
        $this->assertTrue(is_file($path), "Path is not a file [$path]");
    }

    public function testFindRecursive(): void
    {
        $path = $this->pf->find(null, 'foo_types');
        $this->assertFalse(empty($path), "Path is empty [$path]");
        $this->assertTrue(is_string($path), "Path is not a string [$path]");
        $this->assertTrue(file_exists($path), "Path does not exist [$path]");
        $this->assertTrue(is_readable($path), "Path is not readable [$path]");
        $this->assertTrue(is_file($path), "Path is not a file [$path]");
    }

    public function testFindRecursiveFull(): void
    {
        $path = $this->pf->find(null, 'foo_types.csv');
        $this->assertFalse(empty($path), "Path is empty [$path]");
        $this->assertTrue(is_string($path), "Path is not a string [$path]");
        $this->assertTrue(file_exists($path), "Path does not exist [$path]");
        $this->assertTrue(is_readable($path), "Path is not readable [$path]");
        $this->assertTrue(is_file($path), "Path is not a file [$path]");
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testFindExceptionModuleEmpty(): void
    {
        $path = $this->pf->find(null);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testFindExceptionPathNotString(): void
    {
        $path = $this->pf->find('Foo', ['foo' => 'bar']);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testFindExceptionPathNotExist(): void
    {
        $path = $this->pf->find('Common', 'no_such_list');
    }

    public function testFindCommonFallback(): void
    {
        $path = $this->pf->find('Foo', 'genders');
        $this->assertFalse(empty($path), "Path is empty [$path]");
        $this->assertTrue(is_string($path), "Path is not a string [$path]");
        $this->assertTrue(file_exists($path), "Path does not exist [$path]");
        $this->assertTrue(is_readable($path), "Path is not readable [$path]");
        $this->assertTrue(is_file($path), "Path is not a file [$path]");
    }
}
