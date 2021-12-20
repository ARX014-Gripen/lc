<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Orderer $orderer
 */
?>
<?php $this->assign('title', '商品変更'); ?>
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
                    <?= $this->Html->link(
                        "商品登録",['action' => 'add'],['class' => 'button is-success has-text-weight-bold']
                    ) ?>                 
                </span>
                <span class="navbar-item">
                    <?= $this->Html->link("直販", ['controller' => 'Admin','action' => 'reader'],['class'=>'button is-success has-text-weight-bold']) ?>               
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
        <div class="column is-half">
          <?= $this->Form->create($Item,['class'=>'box is-centered is-4','enctype' => 'multipart/form-data']) ?>
            <div class="field">
              <label class="label is-size-5">商品変更</label>
            </div>
            <div class="field">
              <label class="label">商品名</label>
              <div class="control">
                <?= $this->Form->text("name",['placeholder'=>'商品名を入力してください','class'=>'input','required'=>true]) ?>
              </div>
              <?php echo $this->Form->error('name') ?>
            </div>
            <div class="field">
              <label for="" class="label">画像</label>
              <div class="card">
                <div class="card-image">
                  <figure class="image is-4by3">
                  <?php echo "<img src=\"".$this->Url->build("/img/").h($Item->image)."\" alt=\"".h($Item->image)."\">" ?>
                  </figure>
                </div>
              </div>
              <div class="control">
                <input type="file" name="image" accept="image/*">
              </div>
              <?php echo $this->Form->error('image') ?>
            </div>
            <div class="field">
              <label for="" class="label">タグ</label>
              <div class="control">
                <div class="select is-multiple">
                    <select multiple size="3" name="tags[]" required>
                        <?php
                            foreach ($Tags as $Tag):
                                $count=0;
                                foreach ($selectTags as $selectTag):
                                    if($selectTag->tag_name==$Tag->name){
                                        $count++;
                                    }
                                endforeach;
                                if($count>0){
                                    echo "<option value=".h($Tag->id)." selected>".h($Tag->name)."</option>";
                                }else{
                                    echo "<option value=".h($Tag->id).">".h($Tag->name)."</option>";
                                }
                            endforeach;
                        ?>
                    </select>
                </div>
              </div>
              <?php echo $this->Form->error('tags') ?>
            </div>
            <div class="has-text-centered">
              <div class="field">
                <?= $this->Form->button('登録',['class'=>'button is-success submit-button']); ?>
              </div>
            </div>
            <input type="hidden" name="ticket" value="<?=$ticket?>">
          <?= $this->Form->end() ?>
        </div>
    </div>
</section>

