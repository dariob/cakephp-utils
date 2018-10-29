<?php
namespace Qobo\Utils\Test\TestCase\ModuleConfig;

use Cake\TestSuite\TestCase;
use Qobo\Utils\ModuleConfig\ClassFactory;
use Qobo\Utils\ModuleConfig\ClassType;
use Qobo\Utils\ModuleConfig\ConfigType;
use Qobo\Utils\ModuleConfig\ModuleConfig;

class ClassFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $result = ClassFactory::create(ConfigType::MIGRATION(), ClassType::PARSER());
        $this->assertTrue(is_object($result), "create() returned a non-object result");
    }

    /**
     * @expectedException RuntimeException
     */
    public function testCreateBadClassMapException(): void
    {
        $result = ClassFactory::create('BadConfigType', ClassType::PARSER());
    }

    /**
     * @expectedException RuntimeException
     */
    public function testCreateNoClassException(): void
    {
        $options = [
            'classMapVersion' => 'V1',
            'classMap' => [
                'V1' => [
                    'Foo' => [
                        (string)ClassType::PARSER() => '\\This\\Class\\Does\\Not\\Exist',
                    ],
                ],
            ],
        ];

        $result = ClassFactory::create('Foo', ClassType::PARSER(), $options);
    }
}
