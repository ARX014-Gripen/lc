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
        $this->loadModels(['Deliverer','OrderList']);

        $deliverer = $this->Deliverer->find()->where(['id' => $this->Auth->user('id')])->first();

        $orderList = $this->paginate($this->OrderList->find('all'
        )->contain([
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
            'item_name'=>'OrderList.item_name',
            'address'=>'Orderer.address',
            'delivery_date'=>'OrderList.delivery_date',
         ])->where(['OrderList.deliverer_id' => $this->Auth->user('id'),'OrderList.status' => 'ordered']
         )->distinct('OrderList.id')->order(['groupOrder_delivery_date' => 'ASC','groupOrder_orderer_id'=>'ASC']));

        $this->set(compact('deliverer','orderList'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $deliverer = $this->Deliverer->newEntity();
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
                
                $deliverer = $this->Deliverer->patchEntity($deliverer, $this->request->getData());
                if ($this->Deliverer->save($deliverer)) {
                    $this->Flash->success(__('配達者情報の登録が完了しました。'));
                
                    return $this->redirect(['action' => 'index']);
                }
                $this->Flash->error(__('配達者情報の登録に失敗しました。'));
            }
        }
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
        $deliverer = $this->Deliverer->get($id, [
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
    
                $deliverer = $this->Deliverer->patchEntity($deliverer, $this->request->getData());
                if ($this->Deliverer->save($deliverer)) {
                    $this->Flash->success(__('配達者情報の変更が完了しました。'));
    
                    return $this->redirect(['action' => 'index']);
                }
                $this->Flash->error(__('配達者情報の変更に失敗しました。'));    
            }
        }
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
        $this->loadModels(['OrderList']);

        $id = $this->request->getQuery('id');

        $order = $this->OrderList->get($id, [
            'contain' => [],
        ]);

        $order->status = "delivered";

        if ($this->OrderList->save($order)) {
            $this->Flash->success(__('ID'.$id.'の注文の配達を完了しました。'));

            return $this->redirect(['action' => 'index']);
        }
        $this->Flash->error(__('ID'.$id.'の注文の配達を完了に失敗しました。'));
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
        if (isset($user['role']) && ($user['role'] === 'deliverer'||$user['role'] === 'admin')) {
            return true;
        }

        // Default deny
        return false;
    }
}
