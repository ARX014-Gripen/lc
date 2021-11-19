<?php
use Migrations\AbstractMigration;

class Tags extends AbstractMigration
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
        $table = $this->table('na_nakamura_local_tags');
    
        // 各カラムの定義を追加
        $table->addColumn('name', 'string', [
            'default' => null,
            'limit' => 45,
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

        // UNIQUE制約を定義に追加
        $table->addIndex('name', ['unique' => true]);
    
        // テーブルを作成
        $table->create();
    }
}
