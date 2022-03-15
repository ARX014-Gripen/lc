<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Datasource\ConnectionManager;



/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 *
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ItemsController extends AppController
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

        // フリーワードとタグの取得
        if($this->request->getQuery()==null){
            $keyword = null;
            $selectTags = ['タグ検索を行わない'];
        }else{
            $keyword = $this->request->getQuery('keyword');
            if($this->request->getQuery('tags')==null){
                $selectTags = ['タグ検索を行わない'];
            }else{
                $selectTags = $this->request->getQuery('tags');
            }
        }

        // 外部モデル呼び出し
        $this->loadModels(['Items','Tags','ItemsToTags']);

        // 商品一覧を取得
        // ・商品一覧、タグ一覧、連関エンティティの結合表、アイテムIDでグループ化された商品一覧の自己結合
        // ・フリーワード検索付き
        // ・タグ検索付き
        // ※タグ検索の有無でmatchingとgroupの内容を変更
        //  「this is incompatible with sql_mode=only_full_group_by」が発生したため
        //  「only_full_group_by」をoffにしてあります
        if($selectTags[0]=='タグ検索を行わない'){
            // キーワード検索のみ

            $Items = $this->paginate(
                $this->Items->find('all',[
                    'conditions' => [
                        'Items.name LIKE' => '%'.$keyword.'%',
                    ],
                    'group' => 'Items.id'
                ])->matching(
                    "Tags", function($q){
                        return $q;
                    }
                )->select([
                    'item_id' => 'Items.id',
                    'item_name' => 'Items.name',
                    'item_image' => 'Items.image', 
                    'tag_names' => 'group_concat(Tags.name SEPARATOR ",")'
                ])
            );    
        }else{
            // タグとキーワードのよる検索
            // タグリンクのクリック時

            $subqueryA = $this->ItemsToTags->find(
            )->contain([
                'Tags'
            ])->where([
                'Tags.name IN' => $selectTags
            ])->select([
                'item_id_from_tag' => 'ItemsToTags.item_id',
                'tag_id_from_tag' => 'ItemsToTags.tag_id'
            ])->group(
                'item_id_from_tag'
            );

            $subqueryB = $this->Items->find(
            )->where([
                'Items.name LIKE' => '%'.$keyword.'%'
            ])->select([
                'item_id_from_item' => 'Items.id'
            ]);


            $Items = $this->paginate(
                $this->Items->find('all',[
                    'group' => 'Items.id having count(Items.id) = '.count($selectTags)
                ])->join([
                    'SearchTagItems' => [
                        'table' => $subqueryA,
                        'type' => 'inner',
                        'conditions' => 'Items.id = SearchTagItems.item_id_from_tag'
                    ],
                    'SearchItems' => [
                        'table' => $subqueryB,
                        'type' => 'inner',
                        'conditions' => 'SearchItems.item_id_from_item = SearchTagItems.item_id_from_tag'
                    ]
                ])->matching(
                    "Tags", function($q){
                        return $q;
                    }
                )->select([
                    'item_id' => 'Items.id',
                    'item_name' => 'Items.name',
                    'item_image' => 'Items.image', 
                    'tag_names' => 'group_concat(Tags.name SEPARATOR ",")'
                ])
            );    
        }

        // タグ一覧の取得
        $Tags = $this->Tags->find('all')->ToArray();

        // タグ検索選択欄の初期化
        $object = new \stdClass();
        $object->id = 0;
        $object->name = "タグ検索を行わない";
        $object->created = date("YmdHis");;
        $object->modified = date("YmdHis");;
        array_unshift( $Tags, $object);

        // テンプレートへのデータをセット
        $this->set(compact('Items','Tags','selectTags'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {

        // 外部モデル呼び出し
        $this->loadModels(['Items','Tags','ItemsToTags']);

        // 新規ユーザー情報の生成
        $Item = $this->Items->newEntity();

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

            // 現在年月日時を取得
            $datetime = date("YmdHis");

            // ファイル保存
            $file = $this->request->getData('image');
            $filePath = '../webroot/img/'.$datetime.$file['name']; 
            move_uploaded_file($file['tmp_name'], $filePath);

            $connection = ConnectionManager::get('default');
            // トランザクション開始
            $connection->begin();
            try {

                // 設定した情報を保存可能な情報に整形
                $data = [
                    'name' => $this->request->getData('name'),
                    'image' => $datetime.$file['name'] 
                ];
                $Item = $this->Items->patchEntity($Item, $data);
            
                // 商品の保存
                if ($this->Items->save($Item)) {
                    // 保存処理に成功した場合
                
                    // 保存した商品のIDを取得
                    $insertId =  $Item->id;

                    // 選択したタグ情報との商品の紐付け
                    $tags = $this->request->getData("tags");
                    foreach($tags as $tag){
                        $ItemToTag = $this->ItemsToTags->newEntity();
                        $ItemToTag->item_id = $insertId;
                        $ItemToTag->tag_id = (int)$tag;
                        $this->ItemsToTags->save($ItemToTag);
                    }
                
                    // 保存処理に成功したことを通知
                    $this->Flash->success(__('商品の登録が完了しました。'));
                
                    // コミット
                    $connection->commit();

                    // 商品一覧へリダイレクト
                    return $this->redirect(['action' => 'index']);
                }

                $connection->rollback();
            
            } catch(\Exception $e) {
                // 例外に対する処理

                // ロールバック
                $connection->rollback();
            }

            // 処理が失敗したことを通知
            $this->Flash->error(__('商品の登録に失敗しました。'));
        }

        // タグ一覧の取得
        $Tags = $this->Tags->find('all');

        // テンプレートへのデータをセット
        $this->set(compact('Item','Tags'));
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
        $this->loadModels(['Items','Tags','ItemsToTags','DeleteImages']);

        // 商品一覧より指定されたIDの商品を取得
        $Item = $this->Items->get($id, [
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

            $connection = ConnectionManager::get('default');
            // トランザクション開始
            $connection->begin();
            try {
                // 画像ファイルに変更があった場合
                $file = $this->request->getData('image');
                if($file['name'] != null){
                
                    // 現在年月日時を取得
                    $datetime = date("YmdHis");
            
                    // ファイル保存
                    $filePath = '../webroot/img/'.$datetime.$file['name']; 
                    move_uploaded_file($file['tmp_name'], $filePath);

                    // 参照指定書き換え
                    $Item->image = $datetime.$file['name'];
                }

                // 商品名変更
                $Item->name = $this->request->getData('name');

                // 変更した商品の保存
                if ($this->Items->save($Item)) {
                    // 保存処理に成功した場合

                    // 商品に紐づくタグを一旦削除
                    $this->ItemsToTags->deleteAll(['item_id'=>$Item->id],false);

                    // 保存した商品のIDを取得
                    $insertId =  $Item->id;

                    // 選択したタグ情報との商品の紐付け
                    $tags = $this->request->getData("tags");
                    foreach($tags as $tag){
                        $ItemToTag = $this->ItemsToTags->newEntity();
                        $ItemToTag->item_id = $insertId;
                        $ItemToTag->tag_id = (int)$tag;
                        $this->ItemsToTags->save($ItemToTag);
                    }

                    // コミット
                    $connection->commit();

                    // 保存処理に成功したことを通知
                    $this->Flash->success(__('ID'.$id.'の商品情報変更に成功しました。'));
                                    
                    // 商品一覧へリダイレクト
                    return $this->redirect(['action' => 'index']);
                }

                // ロールバック
                $connection->rollback();
            
            } catch(\Exception $e) {
                // 例外に対する処理

                echo $e->getMessage();
                // ロールバック
                $connection->rollback();
            }
          
            // 処理が失敗したことを通知
            $this->Flash->error(__('ID'.$id.'の商品情報変更に失敗しました。'));
        }

        // 商品一覧より指定された商品に紐づいているタグを取得
        $selectTags = $this->ItemsToTags->find('all',[
            'conditions' => [
                'ItemsToTags.item_id' => $Item->id,
            ]
        ])->contain([
            'Tags'
        ])->select([
            'tag_id' => 'Tags.id',
            'tag_name' => 'Tags.name'
        ]);

        // タグ一覧を取得
        $Tags = $this->Tags->find('all');

        // テンプレートへのデータをセット
        $this->set(compact('Item','Tags','selectTags'));
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
        $this->loadModels(['Items','Satisfaction','ItemsToTags']);

        // 商品一覧より指定されたIDの商品を取得
        $Item = $this->Items->get($id, [
            'contain' => [],
        ]);

        // 商品一覧より指定された商品の満足度の遷移を取得
        $satisfaction_result = $this->Satisfaction->find(
            'all'
        )->where([
            'Satisfaction.item_id' => $id
        ])->order(['Satisfaction.delivery_datetime'=>'DESC'])->toList();

        $satisfactions = array();
        $delivery_datetimes = array();
        foreach($satisfaction_result as $key => $result){
            // 満足度リスト作成
            $satisfactions = array_merge(array($result['level']),$satisfactions);
            $delivery_datetimes = array_merge(array(date('Y/m/d',strtotime($result['delivery_datetime'])+(9*60*60))),$delivery_datetimes);
        }

        // 商品一覧より指定された商品に紐づいているタグを取得
        $selectTags = $this->ItemsToTags->find('all',[
            'conditions' => [
                'ItemsToTags.item_id' => $Item->id,
            ]
        ])->contain([
            'Tags'
        ])->select([
            'tag_id' => 'Tags.id',
            'tag_name' => 'Tags.name'
        ]);

        $Tags = array();
        foreach($selectTags as $key => $result){
            // 満足度リスト作成
            $Tags = array_merge(array($result['tag_name']),$Tags);
        }

        // テンプレートへのデータをセット
        $this->set(compact('Item','satisfactions','delivery_datetimes','Tags'));
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
        $this->loadModels(['Items','DeleteImages']);

        // 商品IDの取得
        $id = $this->request->getQuery('id');

        // ユーザー一覧より指定されたIDのユーザーを取得
        $Item = $this->Items->get($id);

        $connection = ConnectionManager::get('default');
        // トランザクション開始
        $connection->begin();
        try {

            // 指定したユーザー情報の削除
            if ($this->Items->delete($Item)) {
                // 削除処理に成功した場合
            
                // 削除処理に成功したことを通知
                $this->Flash->success(__('ID'.$id.'の商品削除が完了しました。'));
            } else {
                // 削除処理に失敗した場合
            
                // 処理が失敗したことを通知
                $this->Flash->error(__('ID'.$id.'の商品削除に失敗しました。'));
            }

            // コミット
            $connection->commit();
        
        } catch(\Exception $e) {
            // 例外に対する処理

            // ロールバック
            $connection->rollback();
        }

        // ユーザ一覧へリダイレクト
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
        // アカウントの役割が「注文者」、「管理者」のみアクセス可能
        if (isset($user['role']) && $user['role'] === 'admin') {
            return true;
        }

        // デフォルトはアクセス不可
        return false;
    }
}
