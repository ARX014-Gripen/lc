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
            $keyword = $this->request->getQuery('keyword');
        }

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
        $User = $this->Users->get($id, [
            'contain' => [],
        ]);

        $this->set('User', $User);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $User = $this->Users->newEntity();
        if ($this->request->is('post')) {
            $User = $this->Users->patchEntity($User, $this->request->getData());

            if ($this->Users->save($User)) {
                $this->Flash->success(__('ユーザーの登録が完了しました。'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('ユーザーの登録に失敗しました。'));
        }
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
        $User = $this->Users->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $User = $this->Users->patchEntity($User, $this->request->getData());
            if ($this->Users->save($User)) {
                $this->Flash->success(__('ID'.$id.'のユーザー情報変更が完了しました。'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('ID'.$id.'のユーザー情報変更に失敗しました。'));
        }
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
        $this->request->allowMethod(['post', 'delete']);
        $User = $this->Users->get($id);
        if ($this->Users->delete($User)) {
            $this->Flash->success(__('ID'.$id.'のユーザー削除が完了しました。'));
        } else {
            $this->Flash->error(__('ID'.$id.'のユーザー削除に失敗しました。'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function subpage()
    {

    }

    public function login()
    {
        if ($this->request->isPost()) {
            $user = $this->Auth->identify();

            if (!empty($user)) {
                $this->Auth->setUser($user);
                if ($this->Auth->authenticationProvider()->needsPasswordRehash()) {
                    $user = $this->Users->get($this->Auth->user('id'));
                    $user->password = $this->request->getData('password');
                    $this->Users->save($user);
                }

                if($user['role'] == 'admin'){
                    return $this->redirect(['controller' => 'Admin', 'action' => 'index']);
                }elseif($user['role'] == 'deliverer'){
                    return $this->redirect(['controller' => 'Deliverer', 'action' => 'index']);
                }elseif($user['role'] == 'orderer'){
                    return $this->redirect(['controller' => 'Orderer', 'action' => 'index']);
                }else{
                    return $this->redirect($this->Auth->redirectUrl());
                }
                
            }
            $this->Flash->error('ユーザー名かパスワードが間違っています。');
        } 
    }

    public function logout(){
        return $this->redirect($this->Auth->Logout());
    }

    public function beforeFilter(Event $event){
        parent::beforeFilter($event);
        // ここにloginを追加してはならない
        // ソース：https://book.cakephp.org/3.0/en/tutorials-and-examples/blog-auth-example/auth.html
        $this->Auth->allow(['add', 'logout','subpage']);
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
