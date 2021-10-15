<?php
namespace App\Shell;

use Cake\Console\Shell;
use Cake\Mailer\Email;

class DiskBatShell extends Shell
{
    public function main()
    {

        $root = '/home/konakera/';
 
        $total = disk_total_space($root);
        $free = disk_free_space($root);
        $used = $total - $free;

        $total = format_bytes($total);
        $free = format_bytes($free);
        $used = format_bytes($used);

        // メール設定
        $email = new Email('Sendgrid');
        $email->setFrom(['konakera@gmail.com' => 'さくらディスク使用量バッチ'])
            ->setTransport('SendgridEmail')
            ->setTo('konakera@i.softbank.jp')
        ->setSubject('ディスク使用量');

        // メール送信
        if($email->send("
全体容量 :{$total}
空き容量 :{$free}
使用容量 :{$used}
        ")){
            // メール送信が成功した場合

        }else{
            // メール送信が失敗した場合

        }
    }
}

/// バイト数を精度指定して単位付きで返す
function format_bytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $i = 0;
    while($bytes > 1024) {
      $bytes /= 1024; $i++;
    }
    return round($bytes, $precision) . ' ' . $units[$i];
}
?>