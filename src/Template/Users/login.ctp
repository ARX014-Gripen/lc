<?php $this->assign('title', 'ログイン'); ?>
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
  <?= $this->Form->create(null,['class'=>'box is-centered is-4']) ?>
    <div class="field">
      <label class="label is-size-5">ログイン</label>
    </div>
    <div class="field">
      <label class="label">Email</label>
      <div class="control has-icons-left has-icons-right">
        <?= $this->Form->text("email",['placeholder'=>'Emailを入力してください','class'=>'input','required'=>true]) ?>
        <span class="icon is-small is-left">
          <i class="fas fa-envelope"></i>
        </span>
      </div>
    </div>
    <div class="field">
      <label for="" class="label">パスワード</label>
      <div class="control has-icons-left">
        <?= $this->Form->text("password",['placeholder'=>'*******','class'=>'input','required'=>true,'type'=>'password']) ?>
        <span class="icon is-small is-left">
          <i class="fa fa-lock"></i>
        </span>
      </div>
    </div>
    <div class="has-text-centered">
      <div class="field">
        <?= $this->Form->button('ログイン',['class'=>'button is-success']); ?>
        <div class="has-text-centered">
            <?= $this->Html->link("ユーザー新規作成",['action' => 'add']) ?>   
        </div>
      </div>
    </div>
  <?= $this->Form->end() ?>
</div>
</section>