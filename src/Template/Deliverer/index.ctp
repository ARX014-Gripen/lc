<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Deliverer[]|\Cake\Collection\CollectionInterface $deliverer
 */
?>
<?php $this->assign('title', '注文一覧'); ?>
<?= $this->Html->script('//cdnjs.cloudflare.com/ajax/libs/jquery-infinitescroll/2.1.0/jquery.infinitescroll.min.js') ?>
<?= $this->Html->script('scroll') ?>
<?= $this->Html->script('burger') ?>
<?= $this->Html->script('pagetop') ?>
<?= $this->Html->css('pagetop') ?>
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
                <?php if ($deliverer != null): ?>
                    <span class="navbar-item">
                        <?= $this->Html->link(
                            "配達者情報変更",['action' => 'edit', $deliverer->id],['class' => 'button is-info has-text-weight-bold']
                        ) ?>  
                    </span>                       
                <?php else: ?>
                    <span class="navbar-item">
                        <?= $this->Html->link(
                            "配達者情報登録",['action' => 'add'],['class' => 'button is-info has-text-weight-bold']
                        ) ?> 
                    </span>   
                <?php endif; ?>                 
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
<p class="pagetop" style="display: block;">
    <a href="#">
        <i class="fas fa-chevron-up"></i>
    </a>
</p>
<section class="section">
    <div class="columns is-centered">
        <h3 class="title has-text-centered is-size-5"><?= __('注文一覧') ?></h3>
    </div>
    <div class="order_list">
        <?php foreach ($orderList as $order): ?>
            <div class="order">
                <div class="columns is-centered">
                    <div class="column is-6-desktop is=8">
                        <div class="card">
                            <div class="card-content">
                                <p class="title is-6">注文ID</p>
                                <p class="subtitle is-6"><?= $this->Number->format($order->order_id) ?></p>
                                <p class="title is-6">配達先ID</p>
                                <p class="subtitle is-6"><?= h($order->orderer_id) ?></p>
                                <p class="title is-6">配達先名</p>
                                <p class="subtitle is-6"><?= h($order->orderer_name) ?></p>
                                <p class="title is-6">アイテム名</p>
                                <p class="subtitle is-6"><?= h($order->item_name) ?></p>
                                <p class="title is-6">配達日</p>
                                <p class="subtitle is-6"><?= h($order->delivery_date) ?></p>
                            </div>
                            <footer class="card-footer">
                                <p class="card-footer-item">
                                    <span>
                                        <?= $this->Html->link(__('ルート検索'),'https://www.google.com/maps/dir/?api=1&destination='.$order->address.'&travelmode=driving', ['class'=>'button is-success has-text-weight-bold','confirm' => __('GoogleMapを開きます。よろしいですか？')]) ?>
                                    </span>
                                </p>
                                <p class="card-footer-item">
                                    <span>
                                        <?= $this->Html->link(__('配送完了'),'https://greatspirit.sakura.ne.jp/NA/k_nakamura/lc/deliverer/delivered?id='.$order->order_id, ['class'=>'button is-danger has-text-weight-bold','confirm' => __(' ID：{0} の配達を完了します。よろしいですか?', $order->order_id)]) ?>
                                    </span>
                                </p>           
                            </footer>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php if ($this->Paginator->param('count') > 3): ?>
        <nav class="pagination columns is-centered" style="margin-top:10px;">
            <?= $this->Paginator->next(__('次の注文情報を表示')) ?>
        </nav>
    <?php endif; ?>
</section>


