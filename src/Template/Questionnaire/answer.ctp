<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User[]|\Cake\Collection\CollectionInterface $Users
 */
?>
<?php $this->assign('title', 'アンケート'); ?>
<?php 
   // ワンタイムチケットを生成する。
    $ticket = md5(uniqid(rand(), true));
    $session = $this->getRequest()->getSession();
    $session->write('ticket',$ticket);
?>
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
        <h3 class="title is-centered is-size-5"><?= __('アンケート') ?></h3>
    </div>
    <div class="columns is-centered">
      <div class="column is-half-desktop">
        <?= $this->Form->create($answer,['class'=>'box is-centered is-4']) ?>
          <div class="field">
            <label class="label is-size-5">満足度調査</label>
          </div>
          <div class="field">
            <label class="label">満足度</label>
            <div class="control">
              <label class="radio">
                <input type="radio" name="answer" value="1">
                1
              </label>
              <label class="radio">
                <input type="radio" name="answer" value="2">
                2
              </label>
              <label class="radio">
                <input type="radio" name="answer" value="3">
                3
              </label>
              <label class="radio">
                <input type="radio" name="answer" value="4">
                4
              </label>
              <label class="radio">
                <input type="radio" name="answer" value="5" checked>
                5
              </label>
            </div>
          </div>
          <div class="has-text-centered">
            <div class="field">
              <?= $this->Form->button('回答',['class'=>'button is-success submit-button']); ?>
            </div>
          </div>
          <input type="hidden" name="item" value="<?=$item?>">
          <input type="hidden" name="order" value="<?=$order?>">
          <input type="hidden" name="expiry" value="<?=$expiry?>">
          <input type="hidden" name="ticket" value="<?=$ticket?>">
        <?= $this->Form->end() ?>
      </div>
    </div>
</section>
