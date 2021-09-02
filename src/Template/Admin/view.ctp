<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $User
 */
?>
<?php $this->assign('title', '注文詳細'); ?>
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
                <?= $this->Html->link("配達者変更", ['action' => 'edit', $fullOrder->id],['class'=>'button is-warning has-text-weight-bold']) ?>               
                </span>
                <span class="navbar-item">
                    <?= $this->Form->postLink(
                        '注文削除',
                        ['action' => 'delete', $fullOrder->id],
                        ['class' => 'button is-danger has-text-weight-bold','confirm' => __('表示中の注文を削除を削除しますか?')]
                        )
                    ?>
                </span>
                <span class="navbar-item">
                    <?= $this->Html->link("注文一覧", ['action' => 'index'],['class'=>'button is-success has-text-weight-bold']) ?>               
                </span>
                <span class="navbar-item">
                    <?= $this->Html->link("ユーザー一覧", ['controller' => 'Users','action' => 'index'],['class'=>'button is-info has-text-weight-bold']) ?>               
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
        <table class="table" style="display: block;overflow-x: scroll;white-space: nowrap;-webkit-overflow-scrolling: touch;">
            <tr>
                <th scope="row"><?= __('注文番号') ?></th>
                <td><?= $this->Number->format($fullOrder->id) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('注文者ID') ?></th>
                <td><?= h($fullOrder->orderer_id) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('注文者名') ?></th>
                <td><?= h($fullOrder->orderer_name) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('配達者ID') ?></th>
                <td><?= h($fullOrder->deliverer_id) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('配達者名') ?></th>
                <td><?= h($fullOrder->deliverer_name) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('商品名') ?></th>
                <td><?= h($fullOrder->item_name) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('ステータス') ?></th>
                <td><?= h($fullOrder->status) ?></td>
            </tr>
        </table>
    </div>
</div>
</section>

