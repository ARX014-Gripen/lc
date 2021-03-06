<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;

/**
 * Tags Controller
 *
 * @property \App\Model\Table\TagsTable $Tags
 *
 * @method \App\Model\Entity\Tag[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class TagsController extends AppController
{
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

        if($this->request->getQuery()==null){
            $keyword = null;
        }else{
            $keyword = $this->request->getQuery('keyword');
        }

        $tags = $this->paginate(
            $this->Tags->find(
                'all'
            )->where([
                'Tags.name LIKE' => '%'.$keyword.'%'
            ])
        );

        $this->set(compact('tags'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $tag = $this->Tags->newEntity();
        if ($this->request->is('post')) {

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

            if(preg_match("/[#,\s]/i",$this->request->getData('name'))){
                $this->Flash->error(__('タグ名に # , スペース は使用できません。'));
            }else{
                $tags = $this->Tags->find(
                    'all'
                )->where([
                    'Tags.name' => $this->request->getData('name')
                ])->toList();
    
                if($tags==null){
                    $tag = $this->Tags->patchEntity($tag, $this->request->getData());
                    if ($this->Tags->save($tag)) {
                        $this->Flash->success(__('タグの登録に成功しました。'));
        
                        return $this->redirect(['action' => 'index']);
                    }
                    
                    $this->Flash->error(__('タグの登録に失敗しました。'));
                }else{
                    $this->Flash->error(__('既にその名前のタグが登録されています。'));
                }
            }
        }
        $items = $this->Tags->Items->find('list', ['limit' => 200]);
        $this->set(compact('tag', 'items'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Tag id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {

        $tag = $this->Tags->get($id, [
            'contain' => ['Items'],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {

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

            if(preg_match("/[#,\s]/i",$this->request->getData('name'))){
                $this->Flash->error(__('タグ名に # , スペース は使用できません。'));
            }else{
                $tags = $this->Tags->find(
                    'all'
                )->where([
                    'Tags.name' => $this->request->getData('name')
                ])->where([
                    'Tags.id IS NOT' => $id
                ])->toList();
    
                if($tags==null){
                    $tag = $this->Tags->patchEntity($tag, $this->request->getData());
                    if ($this->Tags->save($tag)) {
                        $this->Flash->success(__('ID'.$id.'のタグ情報変更に成功しました。'));
        
                        return $this->redirect(['action' => 'index']);
                    }
                    $this->Flash->error(__('ID'.$id.'のタグ情報変更に失敗しました。'));
                }else{
                    $this->Flash->error(__('既にその名前のタグが登録されています。'));
                }
            }
        }
        $items = $this->Tags->Items->find('list', ['limit' => 200]);
        $this->set(compact('tag', 'items'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Tag id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);

        // 外部モデル呼び出し
        $this->loadModels(['Tags','Items','ItemsToTags']);

        // タグIDからタグ名抽出
        $tagName = $this->Tags->find('all')->where(['Tags.id'=>$id])->first();

        // タグによる検索
        $subqueryA = $this->ItemsToTags->find(
        )->contain([
            'Tags'
        ])->where([
            'Tags.name IN' => $tagName->name
        ])->select([
            'item_id_from_tag' => 'ItemsToTags.item_id',
            'tag_id_from_tag' => 'ItemsToTags.tag_id'
        ])->group(
            'item_id_from_tag'
        );

        $tag = $this->Items->find('all',[
            'group' => 'Items.id having count(Items.id) = 1'
        ])->join([
            'SearchTagItems' => [
                'table' => $subqueryA,
                'type' => 'inner',
                'conditions' => 'Items.id = SearchTagItems.item_id_from_tag'
            ]
        ])->matching(
            "Tags", function($q){
                return $q;
            }
        )->select([
            'item_id' => 'Items.id',
            'item_name' => 'Items.name'
        ])->first();

        if($tag){
            $this->Flash->error(__('ID'.$id.'のタグだけが付けられている商品があります。対象の商品削除し、再度実行してください。対象は'.$tag->item_name.'など。'));
        }
        else{
            $tag = $this->Tags->get($id);
            if ($this->Tags->delete($tag)) {
                $this->Flash->success(__('ID'.$id.'のタグ削除が完了しました。'));
            } else {
                $this->Flash->error(__('ID'.$id.'のタグ削除に失敗しました。'));
            }
        }   

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
