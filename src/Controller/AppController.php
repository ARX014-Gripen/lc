<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link https://book.cakephp.org/3/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('Security');`
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('RequestHandler', [
            'enableBeforeRedirect' => false,
        ]);
        $this->loadComponent('Flash');

        // 認証設定
        $this->loadComponent('Auth', [
            'authorize' => ['Controller'], // Added this line
            'authenticate' => [  //送信されたフォームデータのキーとログイン処理の「email」「password」を紐つける設定
                'Form' => [
                    'userModel' => 'Users',
                    'fields' => ['username' => 'email','password' => 'password'],
                    'passwordHasher' => [
                        'className' => 'Fallback',
                        'hashers' => [
                            'Default',
                            'Weak' => ['hashType' => 'sha1']
                        ]
                    ]
                ]
            ],
            'loginAction' => [  //ログイン処理を実行する場所設定
                'controller' => 'Users',
                'action' => 'login'
            ],
            'loginRedirect' => [  //ログイン後のリダイレクト先設定
                'controller' => 'Users',
                'action' => 'login'
            ],
            'logoutRedirect' => [  //ログアウト後のリダイレクト先設定
                'controller' => 'Users',
                'action' => 'login'
            ],
            'unauthorizedRedirect' => [  //認証されていない場合のリダイレクト先設定
                'controller' => 'Users',
                'action' => 'login'
            ],
        ]);

        // PagesController が動作し続けるように
        // display アクションを許可
        $this->Auth->allow(['display']);

        /*
         * Enable the following component for recommended CakePHP security settings.
         * see https://book.cakephp.org/3/en/controllers/components/security.html
         */
        //$this->loadComponent('Security');
    }

    // 外部モデル呼び出しを各コントローラー行えるようにするために
    // 各コントローラで呼び出す予定のAppControllerで設定
    protected function loadModels($models=[])
    {
        foreach ($models as $model) {
            $this->loadModel($model);
        }
    }
}
