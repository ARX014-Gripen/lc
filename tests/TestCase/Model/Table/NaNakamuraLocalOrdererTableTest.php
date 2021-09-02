<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\NaNakamuraLocalOrdererTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\NaNakamuraLocalOrdererTable Test Case
 */
class NaNakamuraLocalOrdererTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\NaNakamuraLocalOrdererTable
     */
    public $NaNakamuraLocalOrderer;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.NaNakamuraLocalOrderer',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('NaNakamuraLocalOrderer') ? [] : ['className' => NaNakamuraLocalOrdererTable::class];
        $this->NaNakamuraLocalOrderer = TableRegistry::getTableLocator()->get('NaNakamuraLocalOrderer', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->NaNakamuraLocalOrderer);

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
}
