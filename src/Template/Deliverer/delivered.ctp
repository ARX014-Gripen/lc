<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Deliverer $deliverer
 */
?>
<?php $this->assign('title', '受け取り署名'); ?>
<?php 
   // ワンタイムチケットを生成する。
    $ticket = md5(uniqid(rand(), true));
    $session = $this->getRequest()->getSession();
    $session->write('ticket',$ticket);
?>
<?= $this->Html->script('canvas') ?>
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
                    <?= $this->Html->link(
                        "配達者情報変更",['action' => 'edit', $deliverer->id],['class' => 'button is-info has-text-weight-bold']
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
<section class="section">
    <div class="columns is-centered">
        <div class="column is-half-desktop">
          <div class="field">
            <label class="label is-size-5">受け取り署名</label>
          </div>
          <div>スマートフォンでの使用を想定しています。</div>
          <div>右側を上にして署名してください。</div>
            <canvas id="draw-area" style="border:1px solid gray;" height="500" width="300"></canvas>
          </div>
          <div>
            <?= $this->Form->create($signature,['name'=>'image_post','enctype' => 'multipart/form-data','onsubmit'=>'return confirm("署名を完了しますか？");']) ?>
                <input type="hidden" type="text" name="signature" value="">
                <input type="hidden" name="ticket" value="<?=$ticket?>">
                <button type="submit" id="post-button" class="button submit-button is-success has-text-weight-bold">署名完了</button>
                <button type="button" id="clear-button" class="button is-danger has-text-weight-bold">署名クリア</button>
            <?= $this->Form->end() ?>
          </div>         
        </div>
    </div>
</section>