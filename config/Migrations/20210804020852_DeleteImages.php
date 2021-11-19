<?php
use Migrations\AbstractMigration;

class DeleteImages extends AbstractMigration
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
        $table = $this->table('na_nakamura_local_delete_images');
    
        // 各カラムの定義を追加
        $table->addColumn('name', 'string', [
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
    
        // テーブルを作成
        $table->create();
    }
}
