<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $User
 */
?>
<?php $this->assign('title', 'ユーザー情報'); ?>
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
                <?= $this->Html->link("ユーザー情報変更", ['action' => 'edit', $User->id],['class'=>'button is-warning has-text-weight-bold']) ?>               
                </span>
                <span class="navbar-item">
                    <?= $this->Form->postLink(
                        'ユーザー削除',
                        ['action' => 'delete', $User->id],
                        ['class' => 'button is-danger has-text-weight-bold','confirm' => __('表示中のユーザーを削除しますか?')]
                        )
                    ?>
                </span>
                <span class="navbar-item">
                    <?= $this->Html->link("直販", ['controller' => 'Admin','action' => 'reader'],['class'=>'button is-success has-text-weight-bold']) ?>               
                </span>
                <span class="navbar-item">
                    <?= $this->Html->link("注文一覧", ['controller' => 'Admin','action' => 'index'],['class'=>'button is-success has-text-weight-bold']) ?>               
                </span>
                <span class="navbar-item">
                    <?= $this->Html->link("BIツール", ['controller' => 'Admin','action' => 'bi'],['class'=>'button is-success has-text-weight-bold']) ?>               
                </span>
                <span class="navbar-item">
                    <?= $this->Html->link("商品一覧", ['controller' => 'Items','action' => 'index'],['class'=>'button is-info has-text-weight-bold']) ?>               
                </span>
                <span class="navbar-item">
                    <?= $this->Html->link("タグ一覧", ['controller' => 'Tags','action' => 'index'],['class'=>'button is-info has-text-weight-bold']) ?>               
                </span>
                <span class="navbar-item">
                    <?= $this->Html->link("ユーザー一覧", ['action' => 'index'],['class'=>'button is-info has-text-weight-bold']) ?>               
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
        <table class="table is-centered" style="display: block;overflow-x: scroll;white-space: nowrap;-webkit-overflow-scrolling: touch;">
            <tr>
                <th scope="row"><?= __('ID') ?></th>
                <td><?= $this->Number->format($User->id) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('メールアドレス') ?></th>
                <td><?= h($User->email) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('役割') ?></th>
                <td><?= h($User->role) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('登録日時') ?></th>
                <td><?= h($User->created) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('更新日時') ?></th>
                <td><?= h($User->modified) ?></td>
            </tr>
        </table>
    </div>
</section>
