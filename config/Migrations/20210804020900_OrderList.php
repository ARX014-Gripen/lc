<?php
use Migrations\AbstractMigration;

class OrderList extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        // idが主キーでついた状態でテーブルを定義
        $table = $this->table('na_nakamura_local_order_list');
    
        // 各カラムの定義を追加
        $table->addColumn('deliverer_id', 'integer', [
            'default' => null,
            'null' => true,
        ]);
        $table->addColumn('orderer_id', 'integer', [
            'default' => null,
            'null' => true,
        ]);
        $table->addColumn('item_id', 'integer', [
            'default' => null,
            'null' => true,
        ]);
        $table->addColumn('delivery_date', 'date', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('status', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
        ]);        
        $table->addColumn('created', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('modified', 'datetime', [
            'default' => null,
            'null' => false,
        ]);

        // 外部キーを定義に追加
        $table->addForeignKey('deliverer_id','na_nakamura_local_deliverer','id',[
            'delete' => 'SET_NULL',
            'update' => 'cascade', 
        ]);
        $table->addForeignKey('orderer_id','na_nakamura_local_orderer','id',[
            'delete' => 'SET NULL',
            'update' => 'cascade', 
        ]);
        $table->addForeignKey('item_id','na_nakamura_local_items','id',[
            'delete' => 'SET_NULL',
            'update' => 'cascade', 
        ]);
    
        // テーブルを作成
        $table->create();
    }
}
