<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
// use Cake\Network\Http\Client;
use Cake\Http\Client;
use Cake\I18n\Time;
use Cake\Database\Type;

/**
 * Orderer Controller
 *
 * @property \App\Model\Table\OrdererTable $Orderer
 *
 * @method \App\Model\Entity\Orderer[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class OrdererController extends AppController
{
    // ページネイションの設定
    public $paginate = [
        'limit' => 3 // 1ページに表示するデータ件数
    ];

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        // 外部モデル呼び出し
        $this->loadModels(['Orderer','OrderList']);

        // ログインしているユーザの注文者情報を取得
        $orderer = $this->Orderer->find()->where(['id' => $this->Auth->user('id')])->first();

        // ログインしているユーザが注文中(未配達)の注文一覧を取得
        // ・注文した順番で降順
        $orderList = $this->paginate($this->OrderList->find('all')->select([ 
            'order_id'=>'OrderList.id',
            'deliverer_id'=>'OrderList.deliverer_id',
            'orderer_id'=>'OrderList.orderer_id',
            'item_name'=>'OrderList.item_name',
            'delivery_date'=>'OrderList.delivery_date',
            'created' => 'OrderList.created'
         ])->where(['orderer_id' => $this->Auth->user('id'),'status' => 'ordered'])->order(['order_id' => 'DESC']));

        // テンプレートへのデータをセット
        $this->set(compact('orderer','orderList'));
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function history()
    {
        // 外部モデル呼び出し
        $this->loadModels(['Orderer','OrderList']);

        // ログインしているユーザの注文者情報を取得
        $orderer = $this->Orderer->find()->where(['id' => $this->Auth->user('id')])->first();

        // ログインしているユーザが注文したの注文一覧を取得
        // ・注文した順番で降順
        $orderList = $this->paginate($this->OrderList->find('all')->select([ 
            'order_id'=>'OrderList.id',
            'deliverer_id'=>'OrderList.deliverer_id',
            'orderer_id'=>'OrderList.orderer_id',
            'item_name'=>'OrderList.item_name',
            'delivery_date'=>'OrderList.delivery_date',
            'status' => 'OrderList.status',
            'created' => 'OrderList.created'
         ])->where(['orderer_id' => $this->Auth->user('id')])->order(['order_id' => 'DESC']));

        // テンプレートへのデータをセット
        $this->set(compact('orderer','orderList'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        // 新規注文者情報の生成
        $orderer = $this->Orderer->newEntity();
        
        // リクエストが「post」であったか確認
        if ($this->request->is('post')) {
            // リクエストが「post」であった場合

            // APIから届け先の座標情報取得
            $url = 'https://www.geocoding.jp/api/?q='.$this->request->getData('address');
            $http = new Client();
            $response = $http->get($url);

            // 取得した座標情報にエラーがあったか確認
            if($response->getXml()->error){
                // GeocodingAPIより情報取得に失敗した場合

                // ジオコーダーの座標取得に失敗したことを通知
                $this->Flash->error(__('ジオコーダーの座標取得に失敗しました。'));
            }else{
                // GeocodingAPIより情報取得に成功した場合

                // APIより取得したXMLを解析
                $results = $response->getXml()->coordinate;

                // 座標を設定
                $this->request = $this->request->withData('id', $this->Auth->user('id'));
                $this->request = $this->request->withData('lat', (float)$results->lat);
                $this->request = $this->request->withData('lng', (float)$results->lng);

                // 設定した情報を保存可能な情報に整形
                $orderer = $this->Orderer->patchEntity($orderer, $this->request->getData());

                // 注文者情報の保存
                if ($this->Orderer->save($orderer)) {
                    // 保存処理に成功した場合

                    // 保存処理に成功したことを通知
                    $this->Flash->success(__('注文者情報の登録が完了しました。'));

                    // 注文一覧にリダイレクト
                    return $this->redirect(['action' => 'index']);
                }

                // 処理が失敗したことを通知
                $this->Flash->error(__('注文者情報の登録に失敗しました。'));
            }
        }

        // テンプレートへのデータをセット
        $this->set(compact('orderer'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Orderer id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        // 注文者情報の取得
        $orderer = $this->Orderer->get($id, [
            'contain' => [],
        ]);

        // リクエストが「edit」であったか確認
        if ($this->request->is(['patch', 'post', 'put'])) {
            // リクエストが「edit」であった場合

            // APIから届け先の座標情報取得
            $url = 'https://www.geocoding.jp/api/?q='.$this->request->getData('address');
            $http = new Client();
            $response = $http->get($url);

            // 取得した座標情報にエラーがあったか確認
            if($response->getXml()->error){
                // GeocodingAPIより情報取得に失敗した場合

                // ジオコーダーの座標取得に失敗したことを通知
                $this->Flash->error(__('ジオコーダーの座標取得に失敗しました。'));
            }else{
                // GeocodingAPIより情報取得に成功した場合

                // APIより取得したXMLを解析
                $results = $response->getXml()->coordinate;
                
                // 座標を設定
                $this->request = $this->request->withData('lat', (float)$results->lat);
                $this->request = $this->request->withData('lng', (float)$results->lng);
                
                // 設定した情報を保存可能な情報に整形
                $orderer = $this->Orderer->patchEntity($orderer, $this->request->getData());

                // 注文者情報の保存
                if ($this->Orderer->save($orderer)) {
                    // 保存処理に成功した場合

                    // 保存処理に成功したことを通知
                    $this->Flash->success(__('注文者情報の変更が完了しました。'));
                
                    // 注文一覧にリダイレクト
                    return $this->redirect(['action' => 'index']);
                }

                // 処理が失敗したことを通知
                $this->Flash->error(__('注文者情報の変更に失敗しました。'));
            }
        }

        // テンプレートへのデータをセット
        $this->set(compact('orderer'));
    }

    /**
     * Order method
     *
     * @return \Cake\Http\Response|null Redirects on successful order, renders view otherwise.
     */
    public function order()
    {
        // 外部モデル呼び出し
        $this->loadModels(['Orderer','Deliverer','OrderList']);

        // 新規注文情報の生成
        $orderList = $this->OrderList->newEntity();

        // リクエストが「post」であったか確認
        if ($this->request->is('post')) {
            // リクエストが「post」であった場合

            // 本日の日付を取得
            $today = date("Y-m-d");

            // 入力された配達日を取得
            $delivery_date = $this->request->getData('delivery_date');

            // 配達日が翌日以降か確認
            if($today>=$delivery_date['year'].'-'.$delivery_date['month'].'-'.$delivery_date['day']){
                // 配達日が注文日以前の場合

                // 配達日が間違っていることを通知
                $this->Flash->error(__('配達日は翌日以降してください。'));
            }else{
                // 配達日が翌日以降の場合

                // ログインしているユーザ(届け先)の情報を取得
                $orderer = $this->Orderer->get($this->Auth->user('id'));

                // 配達者一覧(届け元)を取得
                $deliverers = $this->Deliverer->find('all')->all()->toList();
                
                // 届け先の座標を設定
                $start_lat = $orderer->lat;
                $start_lng = $orderer->lng;
                
                // 各届け元と届け先との距離を計算
                $sortList = array();
                foreach($deliverers as $key => $deliverer){
                
                    // 届け元候補の座標設定
                    $end_key = $deliverer['id'];
                    $end_lat = $deliverer['lat'];
                    $end_lng = $deliverer['lng'];
                
                    // 緯度、経度の移動量を計算
                    $lat_dist = ($start_lat - $end_lat);if($lat_dist<0)$lat_dist=$lat_dist*-1;
                    $lng_dist = ($start_lng - $end_lng);if($lng_dist<0)$lng_dist=$lng_dist*-1;
                
                    // 緯度位置における経度量を計算　地球は丸い
                    $m_lng = 30.9221438 * cos($start_lat / 180 * pi());
                    if($m_lng<0)$m_lng=$m_lng*-1;
                
                    // 移動量を計算
                    $distance = (int)(sqrt(pow(abs($lat_dist / 0.00027778 * 30.9221438), 2) + pow(abs($lng_dist / 0.00027778 * $m_lng), 2)));  
        
                    // 届け元候補リストに情報を追加
                    $sortList = array_merge(array('_'.$end_key=>$distance),$sortList);
                }
        
                // 最寄りの店舗をソート(昇順)で洗い出し
                asort($sortList);
        
                // 最寄り(先頭要素)の店舗情報回収
                $first_value = reset($sortList);
                $first_key = ltrim(key($sortList),'_');
        
                // 配達者と注文完了状態を設定
                $this->request = $this->request->withData('orderer_id', (int)$this->Auth->user('id'));
                $this->request = $this->request->withData('deliverer_id', (int)$first_key);
                $this->request = $this->request->withData('status', 'ordered');
                $this->request = $this->request->withData('delivery_date',$delivery_date['year'].'/'.$delivery_date['month'].'/'.$delivery_date['day']);

                // 設定した情報を保存可能な情報に整形
                $orderList = $this->OrderList->patchEntity($orderList, $this->request->getData());

                // 新規注文情報を保存
                if ($this->OrderList->save($orderList)) {
                    // 保存処理に成功した場合

                    // 保存処理に成功したことを通知
                    $this->Flash->success(__('注文が完了しました。'));
    
                    // 注文一覧にリダイレクト
                    return $this->redirect(['action' => 'index']);
                }

                // 処理が失敗したことを通知
                $this->Flash->error(__('注文に失敗しました。'));
            }
        }

        // テンプレートへのデータをセット
        $this->set(compact('orderList'));
    }    

    // ログアウト
    public function logout(){
        // 認証情報削除してリダイレクト
        // 認証設定によりログイン画面に遷移
        return $this->redirect($this->Auth->Logout());
    }

    // コントローラ呼び出し時の処理
    public function beforeFilter(Event $event){
        parent::beforeFilter($event);
        // 認証で弾くリストからログアウトを除外
        // ここにloginを追加してはならない
        // ソース：https://book.cakephp.org/3.0/en/tutorials-and-examples/blog-auth-example/auth.html
        $this->Auth->allow(['logout']);
    }

    // 認証の設定
    public function isAuthorized($user = null){
        // アカウントの役割が「注文者」、「管理者」のみアクセス可能
        if (isset($user['role']) && ($user['role'] === 'orderer'||$user['role'] === 'admin')) {
            return true;
        }

        // デフォルトはアクセス不可
        return false;
    }
}
