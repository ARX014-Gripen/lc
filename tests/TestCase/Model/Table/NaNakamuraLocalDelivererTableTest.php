<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\NaNakamuraLocalDelivererTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\NaNakamuraLocalDelivererTable Test Case
 */
class NaNakamuraLocalDelivererTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\NaNakamuraLocalDelivererTable
     */
    public $NaNakamuraLocalDeliverer;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.NaNakamuraLocalDeliverer',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('NaNakamuraLocalDeliverer') ? [] : ['className' => NaNakamuraLocalDelivererTable::class];
        $this->NaNakamuraLocalDeliverer = TableRegistry::getTableLocator()->get('NaNakamuraLocalDeliverer', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->NaNakamuraLocalDeliverer);

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
