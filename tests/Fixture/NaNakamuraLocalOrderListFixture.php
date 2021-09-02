<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * NaNakamuraLocalOrderListFixture
 */
class NaNakamuraLocalOrderListFixture extends TestFixture
{
    /**
     * Table name
     *
     * @var string
     */
    public $table = 'na_nakamura_local_order_list';
    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'deliverer_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'orderer_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'item_name' => ['type' => 'string', 'length' => 255, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'status' => ['type' => 'string', 'length' => 255, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        'modified' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        '_indexes' => [
            'deliverer_id' => ['type' => 'index', 'columns' => ['deliverer_id'], 'length' => []],
            'orderer_id' => ['type' => 'index', 'columns' => ['orderer_id'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'na_nakamura_local_order_list_ibfk_1' => ['type' => 'foreign', 'columns' => ['deliverer_id'], 'references' => ['na_nakamura_local_deliverer', 'id'], 'update' => 'cascade', 'delete' => 'setNull', 'length' => []],
            'na_nakamura_local_order_list_ibfk_2' => ['type' => 'foreign', 'columns' => ['orderer_id'], 'references' => ['na_nakamura_local_orderer', 'id'], 'update' => 'cascade', 'delete' => 'cascade', 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'utf8_general_ci'
        ],
    ];
    // @codingStandardsIgnoreEnd
    /**
     * Init method
     *
     * @return void
     */
    public function init()
    {
        $this->records = [
            [
                'id' => 1,
                'deliverer_id' => 1,
                'orderer_id' => 1,
                'item_name' => 'Lorem ipsum dolor sit amet',
                'status' => 'Lorem ipsum dolor sit amet',
                'created' => '2021-08-20 05:30:53',
                'modified' => '2021-08-20 05:30:53',
            ],
        ];
        parent::init();
    }
}
