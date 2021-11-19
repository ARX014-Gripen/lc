<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $User
 */
?>
<?php $this->assign('title', 'ユーザー情報変更'); ?>
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
                    <?= $this->Html->link(
                        "ユーザー新規作成",['action' => 'add'],['class' => 'button is-success has-text-weight-bold']
                    ) ?>                 
                </span>
                <span class="navbar-item">
                    <?= $this->Form->postLink(
                        'ユーザー削除',
                        ['action' => 'delete', $User->id],
                        ['class' => 'button is-danger has-text-weight-bold','confirm' => __('変更中のユーザーを削除を削除しますか?')]
                        )
                    ?>
                </span>
                <span class="navbar-item">
                    <?= $this->Html->link("注文一覧", ['controller' => 'Admin','action' => 'index'],['class'=>'button is-success has-text-weight-bold']) ?>               
                </span>
                <span class="navbar-item">
                    <?= $this->Html->link("BIツール", ['controller' => 'Admin','action' => 'bi'],['class'=>'button is-success has-text-weight-bold']) ?>               
                </span>
                <span class="navbar-item">
                    <?= $this->Html->link("商品一覧", ['controller' => 'Items','action' => 'index'],['class'=>'button is-info has-text-weight-bold']) ?>               
                </span>
                <span class="navbar-item">
                    <?= $this->Html->link("タグ一覧", ['controller' => 'Tags','action' => 'index'],['class'=>'button is-info has-text-weight-bold']) ?>               
                </span>
                <span class="navbar-item">
                    <?= $this->Html->link("ユーザー一覧", ['action' => 'index'],['class'=>'button is-info has-text-weight-bold']) ?>               
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
          <?= $this->Form->create($User,['class'=>'box is-centered is-4']) ?>
            <div class="field">
              <label class="label is-size-5">ユーザ情報変更</label>
            </div>
            <div class="field">
              <label class="label">Email</label>
              <div class="control has-icons-left has-icons-right">
                <?= $this->Form->text("email",['placeholder'=>'Emailを入力してください','class'=>'input','required'=>true]) ?>
                <span class="icon is-small is-left">
                  <i class="fas fa-envelope"></i>
                </span>
              </div>
              <?php echo $this->Form->error('email') ?>
            </div>
            <div class="field">
              <label for="" class="label">パスワード</label>
              <div class="control has-icons-left">
                <?= $this->Form->text("password",['placeholder'=>'*******','class'=>'input','required'=>true,'type'=>'password']) ?>
                <span class="icon is-small is-left">
                  <i class="fa fa-lock"></i>
                </span>
              </div>
              <?php echo $this->Form->error('password') ?>
            </div>
            <div class="has-text-centered">
              <div class="field">
                <?= $this->Form->button('変更',['class'=>'button is-success']); ?>
              </div>
            </div>
          <?= $this->Form->end() ?>
        </div>
    </div>
</section>