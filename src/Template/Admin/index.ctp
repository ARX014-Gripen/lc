<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User[]|\Cake\Collection\CollectionInterface $Users
 */
?>
<?php $this->assign('title', '注文一覧'); ?>
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
            <h3 class="title has-text-centered is-size-5"><?= __('注文一覧') ?></h3>
    </div>
    <div class="columns is-centered">
        <div class="column"></div>
        <div class="column" style="display: flex;justify-content: space-around;">
            <?php echo $this->Form->create(null, ["type" => "get","valueSources" => "query"]); ?>
                <div class="field has-addons">
                    <div class="control"style="width:270px;">
                        <?= $this->Form->text("keyword",['placeholder'=>'検索するキーワードを入力してください。','class'=>'input is-small']); ?>
                    </div>
                    <div class="control">
                        <?php echo $this->Form->button(__("検索"), ["type" => "submit",'class'=>'button is-small is-success has-text-weight-bold']); ?>
                    </div>
                </div>
            <?php echo $this->Form->end(); ?>        
        </div>
        <div class="column"></div>
    </div>
    <div class="columns is-centered">
        <div class="level-item has-text-centered">
            <table class="table is-striped is-centered" cellpadding="0" cellspacing="0" style="display: block;overflow-x: scroll;white-space: nowrap;-webkit-overflow-scrolling: touch;">
                <thead>
                    <tr>
                        <th scope="col"><?= $this->Paginator->sort('order_id','注文ID') ?></th>
                        <th scope="col"><?= $this->Paginator->sort('orderer_id','注文者ID') ?></th>
                        <th scope="col"><?= $this->Paginator->sort('orderer_name','注文者名') ?></th>
                        <th scope="col"><?= $this->Paginator->sort('deliverer_id','配達者ID') ?></th>
                        <th scope="col"><?= $this->Paginator->sort('deliverer_name','配達者名') ?></th>
                        <th scope="col"><?= $this->Paginator->sort('item_name','商品名') ?></th>
                        <th scope="col"><?= $this->Paginator->sort('delivery_date','配達日') ?></th>
                        <th scope="col"><?= $this->Paginator->sort('status','ステータス') ?></th>
                        <th scope="col" class="actions"><?= __('操作') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($fullOrderList as $order): ?>
                        <?php if ($order->deliverer_id == null): ?>
                            <tr class="has-text-danger">
                        <?php else: ?>
                            <tr>
                        <?php endif; ?>  
                            <td><?= $this->Number->format($order->order_id) ?></td>
                            <td><?= h($order->orderer_id) ?></td>
                            <td><?= mb_strimwidth( h($order->orderer_name), 0, 10, '…', 'UTF-8' ); ?></td>
                            <td><?= h($order->deliverer_id) ?></td>
                            <td><?= mb_strimwidth( h($order->deliverer_name), 0, 10, '…', 'UTF-8' ); ?></td>
                            <td><?= mb_strimwidth( h($order->item_name), 0, 10, '…', 'UTF-8' ); ?></td>
                            <td><?= h($order->delivery_date) ?></td>
                            <td><?= h($order->status) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('注文表示'), ['action' => 'view', $order->order_id],['class'=>'button is-small is-info has-text-weight-bold']) ?>
                                <?= $this->Html->link(__('配達者変更'), ['action' => 'edit', $order->order_id],['class'=>'button is-small is-warning has-text-weight-bold']) ?>
                                <?= $this->Form->postLink(__('注文削除'), ['action' => 'delete', $order->order_id], ['class'=>'button is-small is-danger has-text-weight-bold','confirm' => __(' ID：{0} の注文を削除してもよろしいですか?', $order->order_id)]) ?>
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
</div>
