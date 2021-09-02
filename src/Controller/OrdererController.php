<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
// use Cake\Network\Http\Client;
use Cake\Http\Client;

/**
 * Orderer Controller
 *
 * @property \App\Model\Table\OrdererTable $Orderer
 *
 * @method \App\Model\Entity\Orderer[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class OrdererController extends AppController
{
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

        $this->loadModels(['Orderer','OrderList']);

        $orderer = $this->Orderer->find()->where(['id' => $this->Auth->user('id')])->first();

        $orderList = $this->paginate($this->OrderList->find('all')->contain('Orderer')->select([ 
            'order_id'=>'OrderList.id',
            'deliverer_id'=>'OrderList.deliverer_id',
            'orderer_id'=>'OrderList.orderer_id',
            'item_name'=>'OrderList.item_name',
            'address'=>'Orderer.address'
         ])->where(['orderer_id' => $this->Auth->user('id'),'status' => 'ordered'])->order(['order_id' => 'DESC']));

        $this->set(compact('orderer','orderList'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $orderer = $this->Orderer->newEntity();
        if ($this->request->is('post')) {

            // APIから届け先の座標情報取得
            $url = 'https://www.geocoding.jp/api/?q='.$this->request->getData('address');
            $http = new Client();
            $response = $http->get($url);
            if($response->getXml()->error){
                $this->Flash->error(__('ジオコーダーの座標取得に失敗しました。'));
            }else{
                $results = $response->getXml()->coordinate;

                // 座標を設定
                $this->request = $this->request->withData('id', $this->Auth->user('id'));
                $this->request = $this->request->withData('lat', (float)$results->lat);
                $this->request = $this->request->withData('lng', (float)$results->lng);

                $orderer = $this->Orderer->patchEntity($orderer, $this->request->getData());
                if ($this->Orderer->save($orderer)) {
                    $this->Flash->success(__('注文者情報の登録が完了しました。'));

                    return $this->redirect(['action' => 'index']);
                }
                $this->Flash->error(__('注文者情報の登録に失敗しました。'));
            }
        }
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
        $orderer = $this->Orderer->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {

            // APIから届け先の座標情報取得
            $url = 'https://www.geocoding.jp/api/?q='.$this->request->getData('address');
            $http = new Client();
            $response = $http->get($url);
            if($response->getXml()->error){
                $this->Flash->error(__('ジオコーダーの座標取得に失敗しました。'));
            }else{
                $results = $response->getXml()->coordinate;
                
                // 座標を設定
                $this->request = $this->request->withData('lat', (float)$results->lat);
                $this->request = $this->request->withData('lng', (float)$results->lng);
                
                $orderer = $this->Orderer->patchEntity($orderer, $this->request->getData());
                if ($this->Orderer->save($orderer)) {
                    $this->Flash->success(__('注文者情報の変更が完了しました。'));
                
                    return $this->redirect(['action' => 'index']);
                }
                $this->Flash->error(__('注文者情報の変更に失敗しました。'));
            }
        }
        $this->set(compact('orderer'));
    }

    /**
     * Order method
     *
     * @return \Cake\Http\Response|null Redirects on successful order, renders view otherwise.
     */
    public function order()
    {
        $this->loadModels(['Orderer','Deliverer','OrderList']);
        $orderList = $this->OrderList->newEntity();

        if ($this->request->is('post')) {

            $orderer = $this->Orderer->get($this->Auth->user('id'));
            $deliverers = $this->Deliverer->find('all')->all()->toList();
            // 届け先の座標を設定
            $start_lat = $orderer->lat;
            $start_lng = $orderer->lng;
    
            /////////////////////////////
            // 各店舗の距離を計算
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
    
            // 配達者の選定と注文完了状態を設定
            $this->request = $this->request->withData('orderer_id', (int)$this->Auth->user('id'));
            $this->request = $this->request->withData('deliverer_id', (int)$first_key);
            $this->request = $this->request->withData('status', 'ordered');

            $orderList = $this->OrderList->patchEntity($orderList, $this->request->getData());
            if ($this->OrderList->save($orderList)) {
                $this->Flash->success(__('注文が完了しました。'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('注文に失敗しました。'));
        }

        $this->set(compact('orderList'));
    }    

    public function logout(){
        return $this->redirect($this->Auth->Logout());
    }

    public function beforeFilter(Event $event){
        parent::beforeFilter($event);
        // ここにloginを追加してはならない
        // ソース：https://book.cakephp.org/3.0/en/tutorials-and-examples/blog-auth-example/auth.html
        $this->Auth->allow(['logout']);
    }

    public function isAuthorized($user = null){
        // Admin can access every action
        if (isset($user['role']) && ($user['role'] === 'orderer'||$user['role'] === 'admin')) {
            return true;
        }

        // Default deny
        return false;
    }
}
