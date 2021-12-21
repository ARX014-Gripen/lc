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
        // セッションオブジェクトの取得
        $session = $this->getRequest()->getSession();

        // セッション変数を解放し、ブラウザの戻るボタンで戻った場合に備える
        $session->delete('ticket');

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
                            'Items.name LIKE' => '%'.$keyword.'%',
                            'OrderList.status LIKE' => '%'.$keyword.'%',
                            ]
                        ]
                    ]
                ])->contain(['Orderer','Deliverer','Items']
                )->select([ 
                    'order_id'=>'OrderList.id',
                    'orderer_id'=>'Orderer.id',
                    'orderer_name'=>'Orderer.name',
                    'deliverer_id'=>'Deliverer.id',
                    'deliverer_name'=>'Deliverer.name',
                    'item_name'=>'Items.name',
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
        $this->loadModels(['OrderList','Signature']);

        // 1件分の詳細情報付き注文情報を取得
        // ・注文表、注文者、配達者の結合表
        // ・対象の注文番号で検索
        $fullOrder = $this->OrderList->find()->contain(['Orderer','Deliverer','Items'])->select([ 
            'id',
            'orderer_id'=>'Orderer.id',
            'orderer_name'=>'Orderer.name',
            'deliverer_id'=>'Deliverer.id',
            'deliverer_name'=>'Deliverer.name',
            'signature_id'=>'OrderList.signature_id',
            'item_name'=>'Items.name',
            'status'=>'OrderList.status',
        ])->where(['OrderList.id' => $id])->first();

        $signature_img = $this->Signature->find(
            'all'
        )->where([
            'Signature.id' => $fullOrder->signature_id
        ])->first();

        if($signature_img){
            $signature_img = stream_get_contents($signature_img->signature);
        }else{
            $signature_img = null;
        }

        // テンプレートへのデータをセット
        $this->set(compact('fullOrder','signature_img'));
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
        $this->loadModels(['OrderList','Signature']);

        // postかつdelete指定か検査
        $this->request->allowMethod(['post', 'delete']);
        
        // 注文一覧より指定された注文IDの注文を取得
        $orderList = $this->OrderList->get($id);

        if($orderList->orderer_id!=null){
            $signature = $this->Signature->find(
                'all'
            )->where([
                'Signature.id' => $orderList->signature_id
            ])->first();

            if($signature){
                // 指定した注文の削除
                if (!$this->Signature->delete($signature)) {
                    // 削除処理が失敗した場合
                
                    // 削除処理失敗の通知
                    $this->Flash->error(__('ID'.$id.'の注文削除に失敗しました。'));
                
                    // 注文一覧へのリダイレクト
                    return $this->redirect(['action' => 'index']);
                } 
            }  
        }

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
        $this->loadModels(['OrderList','Users','Items','Tags','ItemsToTags','Satisfaction']);

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


        // 注文数ランキング
        // 注文表、配達者の結合表
        $orderList = $this->OrderList->find();
        $deliverer_ranking = $orderList->contain(['Deliverer']
        )->select([
            'order_count' => $orderList->func()->count('*'),
            'deliverer_id' => 'OrderList.deliverer_id',
            'deliverer_name' => 'Deliverer.name'
        ])->where([
            'OrderList.status IS NOT' => 'shop'
        ])->group(
            'OrderList.deliverer_id'
        )->order([
            'order_count' => 'DESC',
            'deliverer_id' => 'ASC'
        ])->limit(10)->all();        

        // 商品ランキング
        // 注文表、商品一覧の結合表
        $orderList = $this->OrderList->find();
        $subqueryA = $orderList->select([
            'item_count' => $orderList->func()->count('*'),
            'item_id_from_order_list' => 'OrderList.item_id'
        ])->group(
            'item_id_from_order_list'
        )->order(['item_count' => 'DESC','item_id_from_order_list'=>'ASC']);

        $item_ranking = $this->Items->find(
            'all'
        )->join([
            'CountItems' => [
                'table' => $subqueryA,
                'type' => 'inner',
                'conditions' => 'Items.id = CountItems.item_id_from_order_list'
            ]
        ])->select([
            'item_name' => 'Items.name',
            'item_count' => 'CountItems.item_count'
        ])->limit(10);


        // タグランキング
        $tags = $this->Tags->find();
        $subqueryA = $this->ItemsToTags->find(
        )->select([
            'item_id_from_items_to_tags' => 'ItemsToTags.item_id',
            'tag_id_from_items_to_tags' => 'ItemsToTags.tag_id'
        ]);
        $subqueryB = $this->OrderList->find(
        )->select([
            'item_id_from_order_list' => 'OrderList.item_id'
        ]);
        
        $tag_ranking = $tags->join([
            'SearchTags' => [
                'table' => $subqueryA,
                'type' => 'left',
                'conditions' => 'Tags.id = SearchTags.tag_id_from_items_to_tags'
            ],
            'SearchItems' => [
                'table' => $subqueryB,
                'type' => 'inner',
                'conditions' => 'SearchTags.item_id_from_items_to_tags = SearchItems.item_id_from_order_list'
            ]
        ])->select([
            'tag_count' => $tags->func()->count('*'),
            'tag_name' => 'Tags.name',
            'tag_id' => 'Tags.id'
        ])->group(
            'tag_name'
        )->order([
            'tag_count' => 'DESC',
            'tag_id' => 'ASC'
        ])->limit(10)->all();


        // アンケート：解答率
        $order_count = $this->OrderList->find('all')->where(['OrderList.status' => 'delivered'])->count();
        $answer_count = $this->Satisfaction->find('all')->count();
        $questionnaire = array();
        $questionnaire_count = array();
        $questionnaire = array_merge(array('不回答'),$questionnaire);
        $questionnaire = array_merge(array('回答'),$questionnaire);
        $questionnaire_count = array_merge(array( $order_count - $answer_count ),$questionnaire_count);
        $questionnaire_count = array_merge(array($answer_count),$questionnaire_count);


        // アンケート：満足度ランキング
        $satisfaction = $this->Satisfaction->find();
        $subqueryA = $satisfaction->select([
            'satisfaction_point' => $satisfaction->func()->sum('level'),
            'item_id_from_satisfaction' => 'Satisfaction.item_id'
        ])->group(
            'item_id_from_satisfaction'
        )->order(['satisfaction_point' => 'DESC','item_id_from_satisfaction'=>'ASC']);

        $satisfaction_ranking = $this->Items->find(
            'all'
        )->join([
            'Point' => [
                'table' => $subqueryA,
                'type' => 'inner',
                'conditions' => 'Items.id = Point.item_id_from_satisfaction'
            ]
        ])->select([
            'item_name' => 'Items.name',
            'item_point' => 'Point.item_id_from_satisfaction'
        ])->limit(10);

        // 注文数ランキング
        // 注文表、配達者の結合表
        $orderList = $this->OrderList->find();
        $shop_ranking = $orderList->contain(['Deliverer']
        )->select([
            'order_count' => $orderList->func()->count('*'),
            'deliverer_id' => 'OrderList.deliverer_id',
            'deliverer_name' => 'Deliverer.name'
        ])->where([
            'OrderList.status' => 'shop'
        ])->group(
            'OrderList.deliverer_id'
        )->order([
            'order_count' => 'DESC',
            'deliverer_id' => 'ASC'
        ])->limit(10)->all();  

         // テンプレートへのデータをセット
         $this->set(compact('deliverer_ranking','role','role_count','item_ranking','tag_ranking','questionnaire','questionnaire_count','satisfaction_ranking','shop_ranking'));

    }

    public function reader()
    {
        if($this->request->getQuery()==null){
            
        }else{
            if(
                $this->request->getQuery('item_id') == null ||
                $this->request->getQuery('deliverer_id') == null ||
                $this->request->getQuery('item_id') == '' ||
                $this->request->getQuery('deliverer_id') == '' 
            ){
                // コードが正しくないことを通知
                $this->Flash->error(__('正しいコードではありません。'));

                echo var_dump($this->request->getQuery());

                // QRコードリーダーへのリダイレクト
                // return $this->redirect(['action' => 'reader']);           
            }



            $item_id = $this->request->getQuery('item_id');
            $deliverer_id = $this->request->getQuery('deliverer_id'); 

            // 外部モデル呼び出し
            $this->loadModels(['OrderList']);

            // 新規注文情報の生成
            $orderList = $this->OrderList->newEntity();

            // 本日の日付を取得
            $today = date("Y-m-d");

            $orderList = $this->OrderList->patchEntity($orderList, $this->request->getData());

            $orderList->deliverer_id = $deliverer_id;
            $orderList->item_id = $item_id;
            $orderList->status = 'shop';
            $orderList->delivery_date = $today;
            $orderList->priority = 2147483647;

            // 新規注文情報を保存
            if ($this->OrderList->save($orderList)) {
                // 保存処理に成功したことを通知
                $this->Flash->success(__('会計が完了しました。次の商品を読み取ってください。'));
            }else{
                // 処理が失敗したことを通知
                $this->Flash->error(__('会計処理に失敗しました。もう一度読み取ってください。'));
            }

            // QRコードリーダーへのリダイレクト
            // return $this->redirect(['action' => 'reader']);
        }
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
        // アカウントの役割が「管理者」のみアクセス可能
        if (isset($user['role']) && $user['role'] === 'admin') {
            return true;
        }

        // デフォルトはアクセス不可
        return false;
    }
}
