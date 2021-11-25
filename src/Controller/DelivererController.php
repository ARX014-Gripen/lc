<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
// use Cake\Network\Http\Client;
use Cake\Http\Client;

/**
 * Deliverer Controller
 *
 * @property \App\Model\Table\DelivererTable $Deliverer
 *
 * @method \App\Model\Entity\Deliverer[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class DelivererController extends AppController
{
    // ページネイションの設定
    public $paginate = [
        'limit' => 6 // 1ページに表示するデータ件数
    ];

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        // セッションオブジェクトの取得
        $session = $this->getRequest()->getSession();

        // セッション変数を解放し、ブラウザの戻るボタンで戻った場合に備える
        $session->delete('ticket');

        // 外部モデル呼び出し
        $this->loadModels(['Deliverer','OrderList']);

        // ログインしているユーザの配達者情報を取得
        $deliverer = $this->Deliverer->find()->where(['id' => $this->Auth->user('id')])->first();

        // ログインしているユーザが配達する注文者情報付き注文一覧を取得
        // ・注文一覧、注文者の結合表、注文日でグループ化された注文一覧の自己結合
        // ・注文日＞注文者IDの優先度で昇順
        $orderList = $this->paginate($this->OrderList->find('all'
        )->contain([
            'Items',
            'Orderer',
            'GroupByOrderList'=>function($q){
                return $q->find('all')->select([
                    'groupOrder_orderer_id'=>'GroupByOrderList.orderer_id',
                    'groupOrder_delivery_date'=>'GroupByOrderList.delivery_date',
                    ])->group('groupOrder_delivery_date');
             }
            ])->select([ 
            'order_id'=>'OrderList.id',
            'deliverer_id'=>'OrderList.deliverer_id',
            'orderer_id'=>'OrderList.orderer_id',
            'orderer_name'=>'Orderer.name',
            'item_name'=>'Items.name',
            'address'=>'Orderer.address',
            'delivery_date'=>'OrderList.delivery_date',
         ])->where(['OrderList.deliverer_id' => $this->Auth->user('id'),'OrderList.status' => 'ordered']
         )->distinct('OrderList.id')->order(['groupOrder_delivery_date' => 'ASC','groupOrder_orderer_id'=>'ASC']));

        // テンプレートへのデータをセット
        $this->set(compact('deliverer','orderList'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {

        // 新規配達者情報の生成
        $deliverer = $this->Deliverer->newEntity();

        // リクエストが「post」であったか確認
        if ($this->request->is('post')) {
            // リクエストが「post」であった場合

            // ポストされたワンタイムチケットを取得する。
            $ticket = $this->request->getData('ticket');

            // セッションオブジェクトの取得
            $session = $this->getRequest()->getSession();

            // セッション変数に保存されたワンタイムチケットを取得する。
            $save = $session->read('ticket');

            // セッション変数を解放し、ブラウザの戻るボタンで戻った場合に備える
            $session->delete('ticket');

            // ポストされたワンタイムチケットの中身が空だった、
            // または、ポストすらされてこなかった場合、
            // 不正なアクセスとみなして強制終了する。
            if ($ticket === '') {
            
                // 不正なアクセスであることを通知
                $this->Flash->error(__('不正なアクセスです。'));

                // 注文一覧にリダイレクト
                return $this->redirect(['action' => 'index']);

            }
        
            // ブラウザの戻るボタンで戻った場合は、セッション変数が存在しないため、
            // 2重送信とみなすことができる。
            // また、不正なアクセスの場合もワンタイムチケットが同じになる確率は低いため、
            // 不正アクセス防止にもなる。
            if($ticket != $save){
            
                // 不正なアクセスであることを通知
                $this->Flash->error(__('二重送信のため処理は実行されませんでした。'));

                // 注文一覧にリダイレクト
                return $this->redirect(['action' => 'index']);
            }            

            // 使用するAPIの仕様で、ごく稀に「緯度0,軽度0」が返ってくることがあるため
            // その場合は正確な座標が返ってくるまで再取得を実施する
            $lat = 0;
            $lng = 0;
            while($lat==0||$lng==0){
                // APIから届け先の座標情報取得
                $url = 'https://www.geocoding.jp/api/?q='.$this->request->getData('address');
                $http = new Client();
                $response = $http->get($url);

                // 取得した座標情報にエラーがあったか確認
                if($response->getXml()->error){
                    // GeocodingAPIより情報取得に失敗した場合

                    // ジオコーダーの座標取得に失敗したことを通知
                    $this->Flash->error(__('ジオコーダーの座標取得に失敗しました。'));
                    
                    // テンプレートへのデータをセット
                    $this->set(compact('deliverer'));

                    return;
                }else{
                    // GeocodingAPIより情報取得に成功した場合

                    // APIより取得したXMLを解析
                    $results = $response->getXml()->coordinate;

                    // 取得したXMLより座標を取得
                    $lat = (float)$results->lat;
                    $lng = (float)$results->lng;
                }
            }

            // 注文者IDと座標の設定
            $this->request = $this->request->withData('id', $this->Auth->user('id'));
            $this->request = $this->request->withData('lat', (float)$lat);
            $this->request = $this->request->withData('lng', (float)$lng);
            
            // 設定した情報を保存可能な情報に整形
            $deliverer = $this->Deliverer->patchEntity($deliverer, $this->request->getData());
            
            // 配達者情報の保存
            if ($this->Deliverer->save($deliverer)) {
                // 保存処理に成功した場合

                // 保存処理に成功したことを通知
                $this->Flash->success(__('配達者情報の登録が完了しました。'));
            
                // 注文一覧へのリダイレクト
                return $this->redirect(['action' => 'index']);
            }

            // 保存処理に失敗したことを通知
            $this->Flash->error(__('配達者情報の登録に失敗しました。'));
        }
        
        // テンプレートへのデータをセット
        $this->set(compact('deliverer'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Deliverer id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {

        // 配達者情報の取得
        $deliverer = $this->Deliverer->get($id, [
            'contain' => [],
        ]);

            // リクエストが「edit」であったか確認
            if ($this->request->is(['patch', 'post', 'put'])) {
                // リクエストが「edit」であった場合
            
            // ポストされたワンタイムチケットを取得する。
            $ticket = $this->request->getData('ticket');
            
            // セッションオブジェクトの取得
            $session = $this->getRequest()->getSession();
            
            // セッション変数に保存されたワンタイムチケットを取得する。
            $save = $session->read('ticket');
            
            // セッション変数を解放し、ブラウザの戻るボタンで戻った場合に備える
            $session->delete('ticket');
            
            // ポストされたワンタイムチケットの中身が空だった、
            // または、ポストすらされてこなかった場合、
            // 不正なアクセスとみなして強制終了する。
            if ($ticket === '') {
            
                // 不正なアクセスであることを通知
                $this->Flash->error(__('不正なアクセスです。'));
                
                // 注文一覧にリダイレクト
                return $this->redirect(['action' => 'index']);
                
            }
        
            // ブラウザの戻るボタンで戻った場合は、セッション変数が存在しないため、
            // 2重送信とみなすことができる。
            // また、不正なアクセスの場合もワンタイムチケットが同じになる確率は低いため、
            // 不正アクセス防止にもなる。
            if($ticket != $save){
            
                // 不正なアクセスであることを通知
                $this->Flash->error(__('二重送信のため処理は実行されませんでした。'));
                
                // 注文一覧にリダイレクト
                return $this->redirect(['action' => 'index']);
            }

            // 使用するAPIの仕様で、ごく稀に「緯度0,軽度0」が返ってくることがあるため
            // その場合は正確な座標が返ってくるまで再取得を実施する
            $lat = 0;
            $lng = 0;
            while($lat==0||$lng==0){
                // APIから届け先の座標情報取得
                $url = 'https://www.geocoding.jp/api/?q='.$this->request->getData('address');
                $http = new Client();
                $response = $http->get($url);

                // 取得した座標情報にエラーがあったか確認
                if($response->getXml()->error){
                    // GeocodingAPIより情報取得に失敗した場合

                    // ジオコーダーの座標取得に失敗したことを通知
                    $this->Flash->error(__('ジオコーダーの座標取得に失敗しました。'));
                    
                    // テンプレートへのデータをセット
                    $this->set(compact('deliverer'));

                    return;
                }else{
                    // GeocodingAPIより情報取得に成功した場合

                    // APIより取得したXMLを解析
                    $results = $response->getXml()->coordinate;

                    // 取得したXMLより座標を取得
                    $lat = (float)$results->lat;
                    $lng = (float)$results->lng;
                }
            }

            // 座標を設定
            $this->request = $this->request->withData('lat', (float)$lat);
            $this->request = $this->request->withData('lng', (float)$lng);
            
            // 設定した情報を保存可能な情報に整形
            $deliverer = $this->Deliverer->patchEntity($deliverer, $this->request->getData());
        
            // 配達者情報の保存
            if ($this->Deliverer->save($deliverer)) {
                // 保存処理に成功した場合
        
                // 保存処理に成功したことを通知
                $this->Flash->success(__('配達者情報の変更が完了しました。'));
            
                // 注文一覧へのリダイレクト
                return $this->redirect(['action' => 'index']);
            }
        
            // 保存処理に失敗したことを通知
            $this->Flash->error(__('配達者情報の変更に失敗しました。'));   
        }

        // テンプレートへのデータをセット
        $this->set(compact('deliverer'));
    }

    /**
     * Delivered method
     *
     * @param string|null $id OrderList id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delivered($id = null)
    {
        // 外部モデル呼び出し
        $this->loadModels(['OrderList']);

        // 注文IDの取得
        $id = $this->request->getQuery('id');

        // 指定された注文IDの注文を取得
        $order = $this->OrderList->get($id, [
            'contain' => [],
        ]);

        // 注文に排他済みを設定
        $order->status = "delivered";

        // 指定した注文を保存
        if ($this->OrderList->save($order)) {
            // 保存が成功した場合

            // 処理成功の通知
            $this->Flash->success(__('ID'.$id.'の注文の配達を完了しました。'));

            // 注文一覧へのリダイレクト
            return $this->redirect(['action' => 'index']);
        }

        // 処理失敗の通知
        $this->Flash->error(__('ID'.$id.'の注文の配達を完了に失敗しました。'));
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
        // アカウントの役割が「配達者」、「管理者」のみアクセス可能
        if (isset($user['role']) && ($user['role'] === 'deliverer'||$user['role'] === 'admin')) {
            return true;
        }

        // デフォルトはアクセス不可
        return false;
    }
}
