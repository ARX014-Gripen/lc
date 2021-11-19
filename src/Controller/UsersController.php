<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;



/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 *
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UsersController extends AppController
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

        // ユーザー一覧を取得
        // ・フリーワード検索付き
        $Users = $this->paginate(
            $this->Users->find('all',[
                'conditions'=>[
                    'AND'=>[
                        'OR'=>[
                            'Users.email LIKE' => '%'.$keyword.'%',
                            'Users.role LIKE' => '%'.$keyword.'%',
                        ]
                    ]
                ]
            ]));

        // テンプレートへのデータをセット
        $this->set(compact('Users'));
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
        // ユーザー一覧より指定されたIDのユーザーを取得
        $User = $this->Users->get($id, [
            'contain' => [],
        ]);

        // テンプレートへのデータをセット
        $this->set('User', $User);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        // 新規ユーザー情報の生成
        $User = $this->Users->newEntity();
        
        // リクエストが「post」であったか確認
        if ($this->request->is('post')) {
            // リクエストが「post」であった場合

            $existing = $this->Users->find(
                'all'
            )->where([
                'Users.email' => $this->request->getData('email')
            ])->toList();

            if($existing==null){
                // 設定した情報を保存可能な情報に整形
                $User = $this->Users->patchEntity($User, $this->request->getData());

                // ユーザー情報の保存
                if ($this->Users->save($User)) {
                    // 保存処理に成功した場合

                    // 保存処理に成功したことを通知
                    $this->Flash->success(__('ユーザーの登録が完了しました。'));

                    // ユーザ一覧へリダイレクト
                    return $this->redirect(['action' => 'index']);
                }

                // 処理が失敗したことを通知
                $this->Flash->error(__('ユーザーの登録に失敗しました。'));
            }else{
                $this->Flash->error(__('そのメールアドレスは既に登録されいます。'));
            }


        }

        // テンプレートへのデータをセット
        $this->set(compact('User'));
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
        // ユーザー一覧より指定されたIDのユーザーを取得
        $User = $this->Users->get($id, [
            'contain' => [],
        ]);

        // リクエストが「edit」であったか確認
        if ($this->request->is(['patch', 'post', 'put'])) {
            // リクエストが「edit」であった場合

            $existing = $this->Users->find(
                'all'
            )->where([
                'Users.email' => $this->request->getData('email')
            ])->where([
                'Users.id IS NOT' => $id
            ])->toList();

            if($existing==null){
                // 設定した情報を保存可能な情報に整形
                $User = $this->Users->patchEntity($User, $this->request->getData());

                // ユーザー情報の保存
                if ($this->Users->save($User)) {
                    // 保存処理に成功した場合
                
                    // 保存処理に成功したことを通知
                    $this->Flash->success(__('ID'.$id.'のユーザー情報変更が完了しました。'));
                
                    // ユーザ一覧へリダイレクト
                    return $this->redirect(['action' => 'index']);
                }
            
                // 処理が失敗したことを通知
                $this->Flash->error(__('ID'.$id.'のユーザー情報変更に失敗しました。'));
            }else{
                $this->Flash->error(__('そのメールアドレスは既に登録されいます。'));
            }
        }

        // テンプレートへのデータをセット
        $this->set(compact('User'));
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
        // postかつdelete指定か検査
        $this->request->allowMethod(['post', 'delete']);

        // ユーザー一覧より指定されたIDのユーザーを取得
        $User = $this->Users->get($id);

        // 指定したユーザー情報の削除
        if ($this->Users->delete($User)) {
            // 削除処理に成功した場合

            // 削除処理に成功したことを通知
            $this->Flash->success(__('ID'.$id.'のユーザー削除が完了しました。'));
        } else {
            // 削除処理に失敗した場合

            // 処理が失敗したことを通知
            $this->Flash->error(__('ID'.$id.'のユーザー削除に失敗しました。'));
        }

        // ユーザ一覧へリダイレクト
        return $this->redirect(['action' => 'index']);
    }

    // サブページ
    public function subpage()
    {

    }

    // ログイン
    public function login()
    {
        // リクエストが「post」であったか確認
        if ($this->request->isPost()) {
            // リクエストが「post」であった場合

            // 現在の認証情報を取得
            $user = $this->Auth->identify();

            // 認証情報があるか確認
            if (!empty($user)) {
                // 認証情報がない場合

                // 認証情報をセット
                $this->Auth->setUser($user);

                // 古くなったハッシュを最新のものに更新
                if ($this->Auth->authenticationProvider()->needsPasswordRehash()) {
                    // パスワードのハッシュを更新した場合

                    // IDとパスワードを設定
                    $user = $this->Users->get($this->Auth->user('id'));
                    $user->password = $this->request->getData('password');

                    // 新しいユーザ情報を保存
                    $this->Users->save($user);
                }

                // 認証情報の役割を元にリダイレクト先を振り分け
                if($user['role'] == 'admin'){
                    // 役割が「管理者」だった場合

                    // AdminControllerへリダイレクト
                    return $this->redirect(['controller' => 'Admin', 'action' => 'index']);
                }elseif($user['role'] == 'deliverer'){
                    // 役割が「配達者」だった場合

                    // DelivererControllerへリダイレクト
                    return $this->redirect(['controller' => 'Deliverer', 'action' => 'index']);
                }elseif($user['role'] == 'orderer'){
                    // 役割が「注文者」だった場合

                    // OrdererControllerへリダイレクト
                    return $this->redirect(['controller' => 'Orderer', 'action' => 'index']);
                }else{
                    // 役割が「管理者」、「配達者」、「注文者」以外の場合

                    // 認証のデフォルトのURLへリダイレクト
                    return $this->redirect($this->Auth->redirectUrl());
                }
                
            }

            // 認証に失敗したことを通知
            $this->Flash->error('ユーザー名かパスワードが間違っています。');
        } 
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
        $this->Auth->allow(['add', 'logout','subpage']);
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
