<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
// use Cake\Network\Http\Client;
use Cake\Http\Client;
use Cake\Mailer\Email;

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
            'priority'=>'OrderList.priority'
         ])->where([
             'OrderList.deliverer_id' => $this->Auth->user('id'),
             'OrderList.status' => 'ordered'
        ])->distinct(
            'OrderList.id'
        )->order([
            'priority'=>'ASC',
            'groupOrder_delivery_date' => 'ASC',
            'groupOrder_orderer_id'=>'ASC'
        ]));

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
        $this->loadModels(['OrderList','Users','Items','Orderer']);

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

            // メール本文に必要な情報を取得
            $orderer = $this->Orderer->get($order->orderer_id);
            $user = $this->Users->get($order->orderer_id);
            $item = $this->Items->get($order->item_id);
            $now = time();
            $expiry_string = $now + (24 * 60 * 60);
            $password = 'hogehoge';
            $cipher = 'AES-256-ECB';
            $encrypted_expiry_string = rawurlencode(openssl_encrypt($expiry_string, $cipher, $password));
            $item_id = rawurlencode(openssl_encrypt($order->item_id, $cipher, $password));
            $order_id = rawurlencode(openssl_encrypt($order->id, $cipher, $password));

            // メール設定
            $email = new Email('Sendgrid');
            $email->setFrom(['konakera@gmail.com' => '配送サービス'])
                ->setTransport('SendgridEmail')
                ->setTo($user->email)
            ->setSubject('アンケート回答のお願い');

            // メール送信
            if($email->send("
{$orderer->name} 様

{$item->name}
をご注文頂き、誠にありがとうございます。

つきましては、お手数ですが
下記のリンクよりアンケートのご回答を
よろしくお願いいたします
https://konakera.sakura.ne.jp/questionnaire/answer?item={$item_id}&order={$order_id}&expiry={$encrypted_expiry_string}
            ")){
                // メール送信が成功した場合

                // 保存処理に成功したことを通知
                $this->Flash->success(__('アンケート依頼送信に成功しました。'));
            }else{
                // メール送信が失敗した場合

                // 処理が失敗したことを通知
                $this->Flash->error(__('アンケート送信に失敗しました。'));
            }
            
            // 処理成功の通知
            $this->Flash->success(__('ID'.$id.'の注文の配達を完了しました。'));

            // 注文一覧へのリダイレクト
            return $this->redirect(['action' => 'index']);
        }

        // 処理失敗の通知
        $this->Flash->error(__('ID'.$id.'の注文の配達を完了に失敗しました。'));
    }

    // 配達順番検索
    public function rootSearch(){

        // 外部モデル呼び出し
        $this->loadModels(['OrderList','Deliverer','Orderer']);

        // 本日までの配達先一覧を取得
        $ordererList = $this->OrderList->find(
            'all'
        )->contain([
            'Orderer'
        ])->where([
            'OrderList.deliverer_id' => $this->Auth->user('id'),
            'OrderList.status' => 'ordered',
            'delivery_date <=' => date("Y-m-d")
        ])->group([
            'OrderList.orderer_id'            
        ])->select([
            'orderer_id' => 'OrderList.orderer_id',
            'name' => 'Orderer.name',
            'address' => 'Orderer.address',
            'lat' => 'Orderer.lat',
            'lng' => 'Orderer.lng'
        ])->toList();

        // 配達開始場所を取得
        $deliverer = $this->Deliverer->get($this->Auth->user('id'), [
            'contain' => [],
        ]);

        // 配達開始場所を格納
        $nodeList = array();        
        array_push(
            $nodeList,
            array(
                'id' => $deliverer->id,
                'name' => $deliverer->name, 
                'address' => $deliverer->address,
                'lat' => $deliverer->lat,
                'lng' => $deliverer->lng
            )
        );

        // 配達先を格納        
        foreach($ordererList as $key => $orderer){
            array_push(
                $nodeList,
                array(
                    'id' => $orderer['orderer_id'],
                    'name' => $orderer['name'],
                    'address' => $orderer['address'],
                    'lat' => $orderer['lat'],
                    'lng' => $orderer['lng']
                )
            );
        }
        
        // 移動距離の総和が一番短い配達順番を算出
        // 巡回セールスマン問題を使用
        $results = solve($nodeList);

        // 配達順番が手前の注文から順に高優先度を設定
        foreach($results as $key => $result){
            $this->OrderList->updateAll(
                [
                    'priority'=>($key+1)
                ],
                [
                    'deliverer_id'=>$this->Auth->user('id'),
                    'orderer_id'=>$result['id'],
                    'status'=>'ordered',
                    'delivery_date <='=>date("Y-m-d")
                ]
            );
        }

        // 注文一覧へのリダイレクト
        return $this->redirect(['action' => 'index']);

    }

    // ログアウト
    public function logout(){
        // セッションオブジェクトの取得
        $session = $this->getRequest()->getSession();

        // セッション変数を解放し、ブラウザの戻るボタンで戻った場合に備える
        $session->delete('ticket');        

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

// 以下巡回セールスマン問題のGreedy法(Kruskalの最小全域木構成アルゴリズム)による解法のメソッド郡
function solve($data) {
    // 今回は始点があるのでコメントアウト
    // array_unshift($data, [0,0,0,0,0]);
    $n = count($data);

    // $links[$i] : $data[$i] と接続されているノード番号
    // $data[0] を端点とするためダミーノードと接続済みとして処理する
    $links = array_fill(0, $n, []);
    $links[0][0] = PHP_INT_MAX;

    // $edges[$i] :
    //   $data[$i] の次数が 0 (未接続) の場合は $i
    //   $data[$i] の次数が 1 (端点) の場合は反対側の端点のノード番号
    //   $data[$i] の次数が 2 (中間点) の場合は -1
    $edges = range(0, $n - 1);
    $edges[0] = PHP_INT_MAX;

    // $distances : ノード間の距離を保持する二次元配列
    //   $distances[*][0] 一方のノード番号
    //   $distances[*][1] もう一方のノード番号
    //   $distances[*][2] ノード間の距離
    $distances = make_distances($data);
    usort($distances, function ($a, $b) { return $a[2] <=> $b[2]; });
    foreach ($distances as [$i, $j, $distance]) {
        connect($data, $i, $j, $edges, $links);
    }

    return build_answer($data, $links);
}

function make_distances($data) {
    $distances = [];
    $n = count($data);
    for ($i = 0; $i < $n; ++$i) {
        for ($j = $i + 1; $j < $n; ++$j) {
            // 届け先の座標を設定
            $start_lat = $data[$i]['lat'];
            $start_lng = $data[$i]['lng'];

            // 届け元候補の座標設定
            $end_lat = $data[$j]['lat'];
            $end_lng = $data[$j]['lng'];
            
            // 緯度、経度の移動量を計算
            $lat_dist = ($start_lat - $end_lat);if($lat_dist<0)$lat_dist=$lat_dist*-1;
            $lng_dist = ($start_lng - $end_lng);if($lng_dist<0)$lng_dist=$lng_dist*-1;
            
            // 緯度位置における経度量を計算　地球は丸い
            $m_lng = 30.9221438 * cos($start_lat / 180 * pi());
            if($m_lng<0)$m_lng=$m_lng*-1;
            
            // 移動量を計算
            $distance = (int)(sqrt(pow(abs($lat_dist / 0.00027778 * 30.9221438), 2) + pow(abs($lng_dist / 0.00027778 * $m_lng), 2)));  

            $distances[] = [$i, $j, $distance+1];
        }
    }
    return $distances;
}

function connect($data, $i, $j, &$edges, &$links) {
    // 既存の経路の中間点から枝分かれしてはいけない
    if ($edges[$i] == -1 || $edges[$j] == -1) return;
    // 経路の両端を繋いでループにしてはいけない
    if ($edges[$i] == $j || $edges[$j] == $i) return;

    // $i と $j が繋がるので "$i の反対端 ($ei)" の反対端は "$j の反対端 ($ej)" になる ($j も同様)
    $ei = $edges[$i];
    $ej = $edges[$j];
    $edges[$ei] = $ej;
    $edges[$ej] = $ei;
    // この接続により $i が中間点になる場合を考慮する ($j も同様)
    // $i が未接続だった場合は $i == $ei なので手前の処理で正しく更新済み
    if ($ei != $i) $edges[$i] = -1;
    if ($ej != $j) $edges[$j] = -1;

    $links[$i][] = $j;
    $links[$j][] = $i;
}

function build_answer($data, $links) {
    $answer = [];
    $prev = PHP_INT_MAX;
    $curr = 0;
    while (count($links[$curr]) == 2) {
        if (($next = $links[$curr][0]) == $prev) $next = $links[$curr][1];
        $prev = $curr;
        $curr = $next;
        $answer[] = $data[$curr];
    }
    return $answer;
}