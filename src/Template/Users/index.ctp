<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User[]|\Cake\Collection\CollectionInterface $Users
 */
?>
<?php $this->assign('title', 'ユーザー一覧'); ?>
<?= $this->Html->script('burger') ?>
<section class="hero is-small" style="background-color:orange">
    <div class="hero-body">
        <div class="navbar-brand">
            <p class="title">
                配送サービス
            </p>
            <span class="navbar-burger burger" data-target="navbarMenuHeroC">
                <span></span>
                <span></span>
                <span></span>
            </span>
        </div>
        <div id="navbarMenuHeroC" class="navbar-menu" style="background-color:orange">
            <div class="navbar-end">
                <span class="navbar-item">
                    <?= $this->Html->link(
                        "ユーザー新規作成",['action' => 'add'],['class' => 'button is-success has-text-weight-bold']
                    ) ?>                 
                </span>
                <span class="navbar-item">
                    <?= $this->Html->link("注文一覧", ['controller' => 'Admin','action' => 'index'],['class'=>'button is-success has-text-weight-bold']) ?>               
                </span>
                <span class="navbar-item">
                    <?= $this->Html->link(
                        "ログアウト",['action' => 'logout'],['class' => 'button has-text-weight-bold']
                    ) ?>
                </span>
            </div>
        </div>
    </div>
</section>
<?= $this->Flash->render() ?>
<section class="section">
    <div class="columns is-centered">
        <h3 class="title is-centered is-size-5"><?= __('ユーザー一覧') ?></h3>
    </div>
    <div class="columns  is-centered">
        <div class="column"></div>
        <div class="column" style="display: flex;justify-content: space-around;">
            <?php echo $this->Form->create(null, ["type" => "get","valueSources" => "query"]); ?>
                <div class="field has-addons">
                    <div class="control"style="width:270px;">
                        <?= $this->Form->text("keyword",['placeholder'=>'検索するキーワードを入力してください。','class'=>'input is-small']); ?>
                    </div>
                    <div class="control">
                        <?= $this->Form->button(__("検索"), ["type" => "submit",'class'=>'button is-small is-success has-text-weight-bold']); ?>
                    </div>
                </div>
            <?= $this->Form->end(); ?>        
        </div>
        <div class="column"></div>
    </div>
    <div class="columns is-centered">
        <div class="level-item has-text-centered">
            <table class="table is-striped is-centered" cellpadding="0" cellspacing="0" style="display: block;overflow-x: scroll;white-space: nowrap;-webkit-overflow-scrolling: touch;">
                <thead>
                    <tr>
                        <th scope="col"><?= $this->Paginator->sort('id','ID') ?></th>
                        <th scope="col"><?= $this->Paginator->sort('email','メールアドレス') ?></th>
                        <th scope="col"><?= $this->Paginator->sort('role','役割') ?></th>
                        <th scope="col"><?= $this->Paginator->sort('created','登録日時') ?></th>
                        <th scope="col"><?= $this->Paginator->sort('modified','更新日時') ?></th>
                        <th scope="col" class="actions"><?= __('操作') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($Users as $User): ?>
                    <tr>
                        <td><?= $this->Number->format($User->id) ?></td>
                        <td><?= mb_strimwidth( h($User->email), 0, 30, '…', 'UTF-8' ); ?></td>
                        <td><?= h($User->role) ?></td>
                        <td><?= h($User->created) ?></td>
                        <td><?= h($User->modified) ?></td>
                        <td class="actions">
                            <?= $this->Html->link(__('表示'), ['action' => 'view', $User->id],['class'=>'button is-small is-info has-text-weight-bold']) ?>
                            <?= $this->Html->link(__('変更'), ['action' => 'edit', $User->id],['class'=>'button is-small is-warning has-text-weight-bold']) ?>
                            <?= $this->Form->postLink(__('削除'), ['action' => 'delete', $User->id], ['class'=>'button is-small is-danger has-text-weight-bold','confirm' => __(' ID：{0} のユーザーを削除してもよろしいですか?', $User->id)]) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="columns is-centered">
        <div class="level-item has-text-centered">
             <?= $this->Paginator->first(__('最初')) ?>
             <?= $this->Paginator->prev(__('前')) ?>
             <?= $this->Paginator->numbers() ?>
             <?= $this->Paginator->next(__('次')) ?>
             <?= $this->Paginator->last(__('最後')) ?>
        </div>
    </div>
    <div class="columns is-centered">
        <div class="level-item has-text-centered">
            <p><?= $this->Paginator->counter(['format' => __('{{page}} / {{pages}}ページ, 全{{count}}件')]) ?></p>
        </div>
    </div>
</section>
