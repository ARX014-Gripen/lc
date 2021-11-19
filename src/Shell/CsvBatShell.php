<?php
namespace App\Shell;

use Cake\Console\Shell;

class CsvBatShell extends Shell
{
    public function initialize()
    {
        parent::initialize();
        $this->loadModel('OrderList');
    }

    /**
    * main() method.
    *
    * @return bool|int|null Success or error code.
    */
    public function main()
    {
        // ログインしているユーザが配達する注文者情報付き注文一覧を取得
        // ・注文一覧、注文者、配達者の結合表、注文日でグループ化された注文一覧の自己結合
        // ・注文日＞注文者IDの優先度で昇順
        $orderList = $this->OrderList->find('all'
        )->contain([
            'Items',
            'Orderer',
            'Deliverer',
            'GroupByOrderList'=>function($q){
                return $q->find('all')->select([
                    'groupOrder_orderer_id'=>'GroupByOrderList.orderer_id',
                    'groupOrder_delivery_date'=>'GroupByOrderList.delivery_date',
                    ])->group('groupOrder_delivery_date');
             }
            ])->select([ 
            'order_id'=>'OrderList.id',
            'deliverer_id'=>'OrderList.deliverer_id',
            'deliverer_name'=>'Deliverer.name',
            'deliverer_address'=>'Deliverer.address',
            'orderer_id'=>'OrderList.orderer_id',
            'orderer_name'=>'Orderer.name',
            'orderer_address'=>'Orderer.address',
            'item_id'=>'OrderList.item_id',
            'item_name'=>'Items.name',
            'delivery_date'=>'OrderList.delivery_date',
            'status'=>'OrderList.status',
         ])->distinct('OrderList.id')->order(['groupOrder_delivery_date' => 'ASC','groupOrder_orderer_id'=>'ASC']);
        
        // 保存場所とファイルの設定:ToDo：FileZilaで位置を確認
        $file = '/home/konakera/www/webroot/csv/' . date('YmdHis') . '.csv';
                
        // ファイルを書き込み用で開く
        $f = fopen($file, 'w');
                
        // 正常にファイルを開けていれば書き込む
        if($f){
        
            // ヘッダーの出力
            $header = array("注文ID.","注文者ID","注文者名","注文者住所","配達者ID","配達者名","配達者住所","商品ID","商品名","配達日","ステータス");
            fputcsv($f, $header);
        
            // データの出力
            foreach($orderList as $order){
            
                // 出力するデータを整形
                $data = array(
                    $order->order_id
                    , $order->orderer_id
                    , $order->orderer_name
                    , $order->orderer_address
                    , $order->deliverer_id
                    , $order->deliverer_name
                    , $order->deliverer_address
                    , $order->item_id
                    , $order->item_name
                    , $order->delivery_date
                    , $order->status
                );
            
                // ファイルに書き込み
                fputcsv($f, $data);
            
            }
        
            // ファイルを閉じる
            fclose($f);
        
        }else{
        
        }
    
    }
}   

?>