<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Orderer $orderer
 */
?>
<?php $this->assign('title', '注文者情報変更'); ?>
<?php 
   // ワンタイムチケットを生成する。
    $ticket = md5(uniqid(rand(), true));
    $session = $this->getRequest()->getSession();
    $session->write('ticket',$ticket);
?>
<?= $this->Html->script('burger') ?>
<?= $this->Html->script('nomal_submit') ?>
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
                    <?= $this->Html->link("注文一覧", ['action' => 'index'],['class'=>'button is-info has-text-weight-bold']) ?>               
                </span>
                <span class="navbar-item">
                    <?= $this->Html->link("注文履歴", ['action' => 'history'],['class'=>'button is-info has-text-weight-bold']) ?>               
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
        <div class="column is-half-desktop">
          <?= $this->Form->create($orderer,['class'=>'box is-centered is-4']) ?>
            <div class="field">
              <label class="label is-size-5">注文者情報変更</label>
            </div>
            <div class="field">
              <label class="label">名前</label>
              <div class="control">
                <?= $this->Form->text("name",['placeholder'=>'名前を入力してください','class'=>'input','required'=>true]) ?>
              </div>
              <?php echo $this->Form->error('name') ?>
            </div>
            <div class="field">
              <label for="" class="label">住所</label>
              <div class="control has-icons-left">
                <?= $this->Form->text("address",['placeholder'=>'住所を入力してください','class'=>'input','required'=>true]) ?>
                <span class="icon is-small is-left">
                  <i class="fa fa-home"></i>
                </span>
              </div>
              <?php echo $this->Form->error('address') ?>
            </div>
            <div class="has-text-centered">
              <div class="field">
                <?= $this->Form->button('変更',['class'=>'button is-success submit-button']); ?>
              </div>
            </div>
            <input type="hidden" name="ticket" value="<?=$ticket?>">
          <?= $this->Form->end() ?>
        </div>
    </div>
</section>