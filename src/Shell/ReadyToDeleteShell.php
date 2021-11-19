<?php
namespace App\Shell;

use Cake\Console\Shell;
use Cake\Filesystem\Folder;
use Cake\Filesystem\File;

class ReadyToDeleteShell extends Shell
{
    public function initialize()
    {
        parent::initialize();
        $this->loadModel('Items');
        $this->loadModel('DeleteImages');
    }

    /**
    * main() method.
    *
    * @return bool|int|null Success or error code.
    */
    public function main()
    {
        // ディレクトリ内のファイル全件取得
        $dir = new Folder(WWW_ROOT.'img');
        $dirFiles = $dir->find('.*', true);

        // 使用している画像ファイル全権取得
        $files = $this->Items->find('all')->toList();
        $files = array_column($files, 'image');

        // 削除対象を削除リストに追加
        $deleteList = array();
        foreach($dirFiles as $file){
            if(!in_array($file, $files)){
                $deleteImage = $this->DeleteImages->newEntity();
                $data = [
                    'name' => $file,
                ];
                $deleteImage = $this->DeleteImages->patchEntity($deleteImage, $data);
                $this->DeleteImages->save($deleteImage);
            }
        }
    }
}   

?>