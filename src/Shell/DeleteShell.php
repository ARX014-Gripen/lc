<?php
namespace App\Shell;

use Cake\Console\Shell;
use Cake\Filesystem\Folder;
use Cake\Filesystem\File;
use Cake\I18n\Time;

class DeleteShell extends Shell
{
    public function initialize()
    {
        parent::initialize();
        $this->loadModel('DeleteImages');
    }

    /**
    * main() method.
    *
    * @return bool|int|null Success or error code.
    */
    public function main()
    {
        // 現在時刻取得
        $datetime = Time::now();
        $datetime = $datetime->i18nFormat('yyyy-MM-dd');

        // 削除リストの画像ファイル全権取得
        $files = $this->DeleteImages->find(
            'all'
        )->where(function($exp) use($datetime) {
            return $exp->lt('created', $datetime);
        })->toList();

        // 対象画像ファイル削除
        foreach ($files as $file) {
            $file = new File(WWW_ROOT. 'img/' . $file['name']);
            $file->delete();
            $file->close();
        }

        // 削除が完了したファイルを削除リストから削除
        $this->DeleteImages->deleteAll(function($exp) use($datetime) {
            return $exp->lt('created', $datetime);
        });

    }
}   

?>