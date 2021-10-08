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

    // ページネイションの設定
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
        // フリーワードの取得
        if($this->request->getQuery()==null){
            $keyword = null;
        }else{
            $keyword = $this->request->getQuery('keyword');
        }

        // 外部モデル呼び出し
        $this->loadModels(['OrderList']);

        // 詳細情報付き注文表一覧を取得
        // ・フリーワード検索付き
        // ・注文表、注文者、配達者の結合表
        // ・配達者が設定されていない注文が先にきて、注文された順に降順
        $fullOrderList = $this->paginate(
            $this->OrderList->find('all',[
                'conditions'=>[
                    'AND'=>[
                        'OR'=>[
                            'Orderer.name LIKE' => '%'.$keyword.'%',
                            'Deliverer.name LIKE' => '%'.$keyword.'%',
                            'OrderList.item_name LIKE' => '%'.$keyword.'%',
                            'OrderList.status LIKE' => '%'.$keyword.'%',
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
                    'delivery_date'=>'OrderList.delivery_date'
                    ])->order(['deliverer_id IS NULL DESC','order_id' => 'DESC']));

        // テンプレートへのデータをセット
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
        // 外部モデル呼び出し
        $this->loadModels(['OrderList']);

        // 1件分の詳細情報付き注文情報を取得
        // ・注文表、注文者、配達者の結合表
        // ・対象の注文番号で検索
        $fullOrder = $this->OrderList->find()->contain(['Orderer','Deliverer'])->select([ 
            'id',
            'orderer_id'=>'Orderer.id',
            'orderer_name'=>'Orderer.name',
            'deliverer_id'=>'Deliverer.id',
            'deliverer_name'=>'Deliverer.name',
            'item_name'=>'OrderList.item_name',
            'status'=>'OrderList.status',
         ])->where(['OrderList.id' => $id])->first();

        // テンプレートへのデータをセット
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
        // 外部モデル呼び出し
        $this->loadModels(['OrderList','Deliverer']);

        // 注文IDの取得
        if($id == null){
            $id = $this->request->getData('orderId');
        }

        // 指定された注文IDの注文を取得
        $orderList = $this->OrderList->get($id, [
            'contain' => [],
        ]);
        
        // リクエストが「put」であったか確認
        if ($this->request->is('put')) {
            // 配達者変更時

            if($this->request->getData('delivererId')!=null){
                // 注文者IDが入力されていた場合

                // 注文者IDの設定
                $orderList->deliverer_id = $this->request->getData('delivererId');

                // 配達者を変更した注文を保存
                if ($this->OrderList->save($orderList)) {
                    // 保存が成功した場合

                    // 処理成功の通知
                    $this->Flash->success(__('ID'.$id.'の注文の配達者変更が完了しました。'));
                    
                    // 注文一覧へのリダイレクト
                    return $this->redirect(['action' => 'index']);
                }

                // 処理失敗の通知
                $this->Flash->error(__('ID'.$id.'の注文の配達者変更に失敗しました。'));
            }
        }else{
            // 配達者を変更する注文選択時、及び、変更先配達者フリーワード検索時

            // フリーワードの取得
            if($this->request->getQuery()==null){
                $keyword = null;
            }else{
                $keyword = $this->request->getQuery('keyword');
            }

            // 配達者一覧を取得
            // ・フリーワード検索付き
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

        // テンプレートへのデータをセット
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
        // 外部モデル呼び出し
        $this->loadModels(['OrderList']);

        // postかつdelete指定か検査
        $this->request->allowMethod(['post', 'delete']);
        
        // 注文一覧より指定された注文IDの注文を取得
        $orderList = $this->OrderList->get($id);

        // 指定した注文の削除
        if ($this->OrderList->delete($orderList)) {
            // 削除処理が成功した場合

            // 削除処理成功の通知
            $this->Flash->success(__('ID'.$id.'の注文削除が完了しました。'));
        } else {
            // 削除処理が失敗した場合

            // 削除処理失敗の通知
            $this->Flash->error(__('ID'.$id.'の注文削除に失敗しました。'));
        }

        // 注文一覧へのリダイレクト
        return $this->redirect(['action' => 'index']);
    }

    // BIツール画面
    public function bi()
    {
        // 外部モデル呼び出し
        $this->loadModels(['OrderList','Users']);

        // 注文数ランキング
        // 注文表、配達者の結合表
        $orderList = $this->OrderList->find();
        $deliverer_ranking = $orderList->contain(['Deliverer']
            )->select([
                'order_count' => $orderList->func()->count('*'),
                'deliverer_id' => 'OrderList.deliverer_id',
                'deliverer_name' => 'Deliverer.name'
            ])->group('OrderList.deliverer_id')->order(['order_count' => 'DESC'])->limit(5)->all();

        // 役割毎のユーザー数一覧
        $users =  $this->Users->find();
        $users_result = $users->select([
            'role' => 'Users.role',
            'role_count' => $users->func()->count('Users.id')           
        ])->group('Users.role')->all()->toList();

        // 役割毎のユーザー数用パイチャートへ向けた振り分け処理
        $role = array();
        $role_count = array();
        foreach($users_result as $key => $result){
            // 役割リスト作成
            $role = array_merge(array($result['role']),$role);
            // ユーザー数リスト作成
            $role_count = array_merge(array($result['role_count']),$role_count);
        }

         // テンプレートへのデータをセット
         $this->set(compact('deliverer_ranking','role','role_count'));

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
        // アカウントの役割が「管理者」のみアクセス可能
        if (isset($user['role']) && $user['role'] === 'admin') {
            return true;
        }

        // デフォルトはアクセス不可
        return false;
    }
}
