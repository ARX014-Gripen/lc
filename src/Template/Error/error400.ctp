<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User[]|\Cake\Collection\CollectionInterface $Users
 */
?>
<?php $this->assign('title', '注文一覧'); ?>
<section class="hero is-small" style="background-color:orange">
    <div class="hero-body">
        <div class="navbar-brand">
            <p class="title">
                配送サービス
            </p>
        </div>
    </div>
</section>
<?= $this->Flash->render() ?>
<section class="section">
    <div class="columns is-centered">
            <h3 class="title has-text-centered is-size-5"><?= __('ページが存在しませんURLをご確認ください') ?></h3>
    </div>
</section>
