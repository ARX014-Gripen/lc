<?php
use Migrations\AbstractMigration;

class ItemsToTags extends AbstractMigration
{

    // IDカラムの自動生成解除
    public $autoId = false; 

    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        // 独自の主キーを使用するので主キーなしでテーブルを定義
        $table = $this->table('na_nakamura_local_items_to_tags');
    
        // 各カラムの定義を追加
        $table->addColumn('item_id', 'integer', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('tag_id', 'integer', [
            'default' => null,
            'null' => false,
        ]);

        // 主キーを定義に追加
        $table->addPrimaryKey([ 
            'item_id', 
            'tag_id', 
        ]); 

        // 外部キーを定義に追加
        $table->addForeignKey('item_id','na_nakamura_local_items','id',[
            'delete' => 'cascade',
            'update' => 'cascade', 
        ]);
        $table->addForeignKey('tag_id','na_nakamura_local_tags','id',[
            'delete' => 'cascade',
            'update' => 'cascade', 
        ]);
    
        // テーブルを作成
        $table->create();
    }
}
