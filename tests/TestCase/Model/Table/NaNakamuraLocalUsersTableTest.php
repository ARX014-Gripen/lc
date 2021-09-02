<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\NaNakamuraLocalUsersTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\NaNakamuraLocalUsersTable Test Case
 */
class NaNakamuraLocalUsersTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\NaNakamuraLocalUsersTable
     */
    public $NaNakamuraLocalUsers;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.NaNakamuraLocalUsers',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('NaNakamuraLocalUsers') ? [] : ['className' => NaNakamuraLocalUsersTable::class];
        $this->NaNakamuraLocalUsers = TableRegistry::getTableLocator()->get('NaNakamuraLocalUsers', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->NaNakamuraLocalUsers);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
