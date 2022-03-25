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
class QuestionnaireController extends AppController
{
    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function answer()
    {
        // 外部モデル呼び出し
        $this->loadModels(['Satisfaction','OrderList']);

        // 新規回答の生成
        $answer = $this->Satisfaction->newEntity();

        $password = 'hogehoge';
        $cipher = 'AES-256-ECB';

        $item = $this->request->getQuery('item');
        $expiry = $this->request->getQuery('expiry');  
        $order = $this->request->getQuery('order');  

        if(
            $item == null || 
            $order == null || 
            $expiry == null ||
            $item == '' || 
            $order == '' || 
            $expiry == ''
        ){
            // 不正なアクセスであることを通知
            $this->Flash->error(__('不正なアクセスです。'));
            return $this->redirect(['controller' => 'Users','action' => 'login']);
        }

        // リクエストが「post」であったか確認
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
                return $this->redirect(['controller' => 'Users','action' => 'login']);

            }
        
            // ブラウザの戻るボタンで戻った場合は、セッション変数が存在しないため、
            // 2重送信とみなすことができる。
            // また、不正なアクセスの場合もワンタイムチケットが同じになる確率は低いため、
            // 不正アクセス防止にもなる。
            if($ticket != $save){
            
                // 不正なアクセスであることを通知
                $this->Flash->error(__('二重送信のため処理は実行されませんでした。'));

                // 注文一覧にリダイレクト
                return $this->redirect(['controller' => 'Users','action' => 'login']);
            }

            // 現在時刻取得
            $now = time();

            // 暗号化された内容を復号化
            $encrypted_expiry_string = rawurldecode($this->request->getData('expiry'));
            $expiry = openssl_decrypt($encrypted_expiry_string, $cipher, $password);
            $encrypted_item_id = rawurldecode($this->request->getData('item'));
            $item_id = openssl_decrypt($encrypted_item_id, $cipher, $password);
            $encrypted_order_id = rawurldecode($this->request->getData('order'));
            $order_id = openssl_decrypt($encrypted_order_id, $cipher, $password);
    
            // 回答期限チェック
            if( intval($expiry) < $now ) {
                $this->Flash->error(__('回答の有効期限が切れました。'));
                return $this->redirect(['controller' => 'Users','action' => 'login']);
            }

            $order = $this->OrderList->get((int)$order_id);

            // 新規回答の値の設定
            $answer->item_id = $item_id;
            $answer->order_id = $order_id;
            $answer->level = $this->request->getData('answer');
            $answer->delivery_datetime = $order->delivery_date;

            // 配達者情報の保存
            if ($this->Satisfaction->save($answer)) {
                // 保存処理に成功した場合
        
                // 保存処理に成功したことを通知
                $this->Flash->success(__('アンケートの回答が完了しました。'));
            
                // 注文一覧へのリダイレクト
                return $this->redirect(['controller' => 'Users','action' => 'login']);
            }
        
            // 保存処理に失敗したことを通知
            $this->Flash->error(__('アンケートの回答に失敗しました。'));
        }             

        $encrypted_order_id = rawurldecode($this->request->getQuery('order'));
        $order_id = openssl_decrypt($encrypted_order_id, $cipher, $password);

        $existing = $this->Satisfaction->find(
            'all'
        )->where([
            'Satisfaction.order_id' => $order_id
        ])->toList();

        if($existing!=null){
            $this->Flash->error(__('既にご回答いただいています。'));
            return $this->redirect(['controller' => 'Users','action' => 'login']);  
        }

        $this->set(compact('item','expiry','answer','order'));
    }

    // コントローラ呼び出し時の処理
    public function beforeFilter(Event $event){
        parent::beforeFilter($event);
        // 認証で弾くリストからログアウトを除外
        // ここにloginを追加してはならない
        // ソース：https://book.cakephp.org/3.0/en/tutorials-and-examples/blog-auth-example/auth.html
        $this->Auth->allow(['answer']);
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
