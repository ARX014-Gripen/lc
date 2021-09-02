<?php
use Migrations\AbstractMigration;

class Deliverer extends AbstractMigration
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
        // idが主キーでついた状態でテーブルを定義（自動採番なし）
        // $table = $this->table('na_nakamura_delivery_person');
        $table = $this->table('na_nakamura_local_deliverer', ['id' => false, 'primary_key' => 'id']);
        $table->addColumn('id', 'integer');
    
        // 各カラムの定義を追加
        $table->addColumn('name', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
        ]);
        $table->addColumn('address', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
        ]);
        $table->addColumn('lat', 'float', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('lng', 'float', [
            'default' => null,
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
        $table->addForeignKey('id','na_nakamura_local_users','id',[
            'delete' => 'cascade',
            'update' => 'cascade', 
        ]);
    
        // テーブルを作成
        $table->create();
    }
}
