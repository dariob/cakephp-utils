<?php
namespace Qobo\Utils\Test\TestCase\Model\Behavior;

use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Security;
use Qobo\Utils\Model\Behavior\EncryptedFieldsBehavior;
use RuntimeException;

/**
 * Qobo\Utils\Model\Behavior\EncryptedFieldsBehavior Test Case
 */
class EncryptedFieldsBehaviorTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.qobo/utils.users',
    ];

    /**
     * Test subject
     *
     * @var \Qobo\Utils\Model\Behavior\EncryptedFieldsBehavior
     */
    public $EncryptedFields;

    /**
     * Test table
     *
     * @var \Qobo\Utils\Test\App\Model\Table\UsersTable
     */
    public $Users;

    /**
     * Encryption key
     *
     * @var string
     */
    protected $key;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->key = Configure::readOrFail('Qobo/Utils.encryptionKey');
        /** @var \Qobo\Utils\Test\App\Model\Table\UsersTable $table */
        $table = TableRegistry::getTableLocator()->get('Users');
        $this->Users = $table;
        $this->Users->setTable('users');

        $config = [
            'encryptionKey' => $this->key,
            'fields' => [
                'name' => [
                    'decrypt' => true,
                ],
            ],
        ];
        $this->EncryptedFields = new EncryptedFieldsBehavior($this->Users, $config);
        $this->Users->addBehavior('Qobo/Utils.EncryptedFields', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->EncryptedFields);
        unset($this->key);
        unset($this->Users);

        parent::tearDown();
    }

    /**
     * Test encryption key validation during initialization
     *
     * @return void
     */
    public function testInitializeEncryptionKeyValidation(): void
    {
        $this->expectException(RuntimeException::class);
        $this->EncryptedFields->setConfig(['encryptionKey' => '']);
        $this->EncryptedFields->initialize([]);
    }

    /**
     * Test `enabled` validation during initialization
     *
     * @return void
     */
    public function testInitializeEnabledValidationInvalidType(): void
    {
        $this->expectException(RuntimeException::class);
        $this->EncryptedFields->initialize(['enabled' => 'foobar']);
    }

    /**
     * Test `enabled` validation during initialization
     *
     * @return void
     */
    public function testInitializeEnabledValidationValidType(): void
    {
        $this->EncryptedFields->initialize(['enabled' => false]);
        $this->assertFalse($this->EncryptedFields->getConfig('enabled'));

        $this->EncryptedFields->setConfig(['enabled' => function () {
            return false;
        }]);
        $this->EncryptedFields->initialize([]);
        $this->assertTrue(is_callable($this->EncryptedFields->getConfig('enabled')));
    }

    /**
     * Test no encryption is done if behavior is disabled.
     *
     * @return void
     */
    public function testEncryptionDisabled(): void
    {
        $this->EncryptedFields->setConfig([
            'enabled' => false
        ]);
        $this->EncryptedFields->initialize([]);

        $name = 'foobar';
        $entity = $this->Users->newEntity([
            'name' => $name,
        ]);

        $actualEntity = $this->EncryptedFields->encrypt($entity);
        $this->assertSame($entity, $actualEntity);
        $this->assertEquals($entity, $actualEntity);
        $this->assertEquals($name, $actualEntity->get('name'));
    }

    /**
     * Test success field encryption
     *
     * @return void
     */
    public function testEncryptSuccess(): void
    {
        $name = 'foobar';
        $entity = $this->Users->newEntity([
            'name' => $name,
        ]);

        // Assert name was changed
        $actualEntity = $this->EncryptedFields->encrypt($entity);
        $this->assertTrue($actualEntity->isDirty('name'));
    }

    /**
     * Test missing fields are skipped
     *
     * @return void
     */
    public function testEncryptMissingFieldsAreSkipped(): void
    {
        $this->EncryptedFields->setConfig(['fields' => [
                'invalid_field' => [
                    'decrypt' => true,
                ],
            ]
        ]);

        $entity = $this->Users->newEntity();
        $actualEntity = $this->EncryptedFields->encrypt($entity);
        $this->assertSame($entity, $actualEntity);
        $this->assertEquals($entity, $actualEntity);
    }
}
