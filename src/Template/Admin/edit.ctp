<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User[]|\Cake\Collection\CollectionInterface $Users
 */
?>
<?php $this->assign('title', '配達者選択'); ?>
<?php 
   // ワンタイムチケットを生成する。
    $ticket = md5(uniqid(rand(), true));
    $session = $this->getRequest()->getSession();
    $session->write('ticket',$ticket);
?>
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
                    <?= $this->Form->postLink(
                        '注文削除',
                        ['action' => 'delete', $id],
                        ['class' => 'button is-danger has-text-weight-bold','confirm' => __('変更中の注文を削除しますか?')]
                        )
                    ?>
                </span>
                <span class="navbar-item">
                    <?= $this->Html->link("注文一覧", ['action' => 'index'],['class'=>'button is-success has-text-weight-bold']) ?>               
                </span>
                <span class="navbar-item">
                    <?= $this->Html->link("BIツール", ['action' => 'bi'],['class'=>'button is-success has-text-weight-bold']) ?>               
                </span>
                <span class="navbar-item">
                    <?= $this->Html->link("商品一覧", ['controller' => 'Items','action' => 'index'],['class'=>'button is-info has-text-weight-bold']) ?>               
                </span>
                <span class="navbar-item">
                    <?= $this->Html->link("タグ一覧", ['controller' => 'Tags','action' => 'index'],['class'=>'button is-info has-text-weight-bold']) ?>               
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
            <h3 class="title has-text-centered is-size-5"><?= __('配達者一覧') ?></h3>
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
                        <th scope="col"><?= $this->Paginator->sort('id','配達者ID') ?></th>
                        <th scope="col"><?= $this->Paginator->sort('name','配達者名') ?></th>
                        <th scope="col"><?= $this->Paginator->sort('address','住所') ?></th>
                        <th scope="col" class="actions"><?= __('操作') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($Deliverers as $Deliverer): ?>
                    <tr>
                        <td class="has-text-left"><?= $this->Number->format($Deliverer->id) ?></td>
                        <td class="has-text-left"><?= mb_strimwidth( h($Deliverer->name), 0, 10, '…', 'UTF-8' ); ?></td>
                        <td class="has-text-left"><?= mb_strimwidth( h($Deliverer->address), 0, 30, '…', 'UTF-8' ); ?></td>
                        <td class="actions">
                            <?= $this->Form->postLink(__('配達者決定'), ['action' => 'edit',$id], ['method'=>'put','data'=>['delivererId'=>$Deliverer->id,'ticket'=>$ticket],'class'=>'button is-small is-success has-text-weight-bold','confirm' => __(' ID：{0} の配達者に変更してもよろしいですか?', $Deliverer->id)]) ?>
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

