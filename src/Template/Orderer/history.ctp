<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Orderer[]|\Cake\Collection\CollectionInterface $orderer
 */
?>
<?php $this->assign('title', '注文履歴'); ?>
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
                <span class="navbar-item">
                        <?= $this->Html->link(
                            "新規注文",['action' => 'order'],['class' => 'button is-success has-text-weight-bold']
                        ) ?> 
                </span> 
                <span class="navbar-item">
                    <?= $this->Html->link(
                        "注文者情報変更",['action' => 'edit', $orderer->id],['class' => 'button is-info has-text-weight-bold']
                    ) ?>  
                </span>
                <span class="navbar-item">
                    <?= $this->Html->link(
                        "注文一覧",['action' => 'index'],['class' => 'button is-info has-text-weight-bold']
                    ) ?> 
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
<p class="pagetop" style="display: block;">
    <a href="#">
        <i class="fas fa-chevron-up"></i>
    </a>
</p>
<section class="section">
    <div class="columns is-centered">
        <h3 class="title has-text-centered is-size-5"><?= __('注文履歴一覧') ?></h3>
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
                                <p class="title is-6">商品名</p>
                                <p class="subtitle is-6"><?= h($order->item_name) ?></p>
                                <p class="title is-6">配達日</p>
                                <p class="subtitle is-6"><?= h($order->delivery_date) ?></p>
                                <p class="title is-6">ステータス</p>
                                <p class="subtitle is-6"><?= h($order->status) ?></p>
                                <p class="title is-6">購入日時</p>
                                <p class="subtitle is-6"><?= h($order->created) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <?= $this->fetch('postLink') ?>
    </div>
    <?php if ($this->Paginator->param('count') > 3): ?>
        <nav class="pagination columns is-centered" style="margin-top:10px;">
            <?= $this->Paginator->next(__('次の注文情報を表示')) ?>
        </nav>
    <?php endif; ?>  
</section>

