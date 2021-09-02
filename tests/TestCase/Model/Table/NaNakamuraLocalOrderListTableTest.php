<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\NaNakamuraLocalOrderListTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\NaNakamuraLocalOrderListTable Test Case
 */
class NaNakamuraLocalOrderListTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\NaNakamuraLocalOrderListTable
     */
    public $NaNakamuraLocalOrderList;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.NaNakamuraLocalOrderList',
        'app.NaNakamuraLocalDeliverer',
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
        $config = TableRegistry::getTableLocator()->exists('NaNakamuraLocalOrderList') ? [] : ['className' => NaNakamuraLocalOrderListTable::class];
        $this->NaNakamuraLocalOrderList = TableRegistry::getTableLocator()->get('NaNakamuraLocalOrderList', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->NaNakamuraLocalOrderList);

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
