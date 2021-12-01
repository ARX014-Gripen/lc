<?php
use Migrations\AbstractMigration;

class Satisfaction extends AbstractMigration
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
        $table = $this->table('na_nakamura_local_satisfaction');
    
        // 各カラムの定義を追加
        $table->addColumn('order_id', 'integer', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('item_id', 'integer', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('level', 'integer', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('delivery_datetime', 'datetime', [
            'default' => null,
            'null' => false,
        ]);    
        // テーブルを作成
        $table->create();
    }
}
