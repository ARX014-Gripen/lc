<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;

/**
 * Admin Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 *
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class AdminController extends AppController
{

    public $paginate = [
        'limit' => 10 // 1ページに表示するデータ件数
    ];

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        if($this->request->getQuery()==null){
            $keyword = null;
        }else{
            $keyword = $this->request->getQuery()['keyword'];
        }

        $this->loadModels(['OrderList']);

        $fullOrderList = $this->paginate(
            $this->OrderList->find('all',[
                'conditions'=>[
                    'AND'=>[
                        'OR'=>[
                            'Orderer.name LIKE' => '%'.$keyword.'%',
                            'Deliverer.name LIKE' => '%'.$keyword.'%',
                            'OrderList.item_name LIKE' => '%'.$keyword.'%',
                            ]
                        ]
                    ]
                ])->contain(['Orderer','Deliverer']
                )->select([ 
                    'order_id'=>'OrderList.id',
                    'orderer_id'=>'Orderer.id',
                    'orderer_name'=>'Orderer.name',
                    'deliverer_id'=>'Deliverer.id',
                    'deliverer_name'=>'Deliverer.name',
                    'item_name'=>'OrderList.item_name',
                    'status'=>'OrderList.status',
                    ])->order(['deliverer_id IS NULL DESC','order_id' => 'DESC']));

        $this->set(compact('fullOrderList'));
    }

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $this->loadModels(['OrderList']);

        $fullOrder = $this->OrderList->find()->contain(['Orderer','Deliverer'])->select([ 
            'id',
            'orderer_id'=>'Orderer.id',
            'orderer_name'=>'Orderer.name',
            'deliverer_id'=>'Deliverer.id',
            'deliverer_name'=>'Deliverer.name',
            'item_name'=>'OrderList.item_name',
            'status'=>'OrderList.status',
         ])->where(['OrderList.id' => $id])->first();

        $this->set('fullOrder', $fullOrder);
    }

    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $this->loadModels(['OrderList','Deliverer']);

        if($id == null){
            $id = $this->request->getData('orderId');
        }

        $orderList = $this->OrderList->get($id, [
            'contain' => [],
        ]);

        // if ($this->request->is(['patch','post','put'])) {
        if ($this->request->is('put')) {
            if($this->request->getData('delivererId')!=null){
                $orderList->deliverer_id = $this->request->getData('delivererId');
                if ($this->OrderList->save($orderList)) {
                    $this->Flash->success(__('ID'.$id.'の注文の配達者変更が完了しました。'));
                    return $this->redirect(['action' => 'index']);
                }
                $this->Flash->error(__('ID'.$id.'の注文の配達者変更に失敗しました。'));
            }
        }else{
            if($this->request->getQuery()==null){
                $keyword = null;
            }else{
                $keyword = $this->request->getQuery()['keyword'];
            }
            $Deliverers = $this->paginate(
                $this->Deliverer->find('all',[
                    'conditions'=>[
                        'AND'=>[
                             'OR'=>[
                                'name LIKE' => '%'.$keyword.'%',         
                                'address LIKE' => '%'.$keyword.'%',
                                ]
                            ]
                        ]
                    ]));
        }

        $this->set(compact('Deliverers','id'));
    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->loadModels(['OrderList']);

        $this->request->allowMethod(['post', 'delete']);
        $orderList = $this->OrderList->get($id);
        if ($this->OrderList->delete($orderList)) {
            $this->Flash->success(__('ID'.$id.'の注文削除が完了しました。'));
        } else {
            $this->Flash->error(__('ID'.$id.'の注文削除に失敗しました。'));
        }

        return $this->redirect(['action' => 'index']);
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
        if (isset($user['role']) && $user['role'] === 'admin') {
            return true;
        }

        // Default deny
        return false;
    }
}
