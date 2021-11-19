<?php $this->assign('title', '商品一覧'); ?>
<?= $this->Html->script('//cdnjs.cloudflare.com/ajax/libs/jquery-infinitescroll/2.1.0/jquery.infinitescroll.min.js') ?>
<?= $this->Html->script('item_scroll') ?>
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
                        "商品登録",['action' => 'add'],['class' => 'button is-success has-text-weight-bold']
                    ) ?>                 
                </span>
                <span class="navbar-item">
                    <?= $this->Html->link("注文一覧", ['controller' => 'Admin','action' => 'index'],['class'=>'button is-success has-text-weight-bold']) ?>               
                </span>
                <span class="navbar-item">
                    <?= $this->Html->link("BIツール", ['controller' => 'Admin','action' => 'bi'],['class'=>'button is-success has-text-weight-bold']) ?>               
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
<p class="pagetop" style="display: block;">
    <a href="#">
        <i class="fas fa-chevron-up"></i>
    </a>
</p>
<section class="section">
    <div class="container">
        <div class="columns is-centered">
            <h3 class="title is-centered is-size-5"><?= __('商品一覧') ?></h3>
        </div>
        <div class="columns  is-centered">
            <div class="column"></div>
            <div class="column" style="display: flex;justify-content: space-around;">
                <?php echo $this->Form->create(null, ["type" => "get","valueSources" => "query"]); ?>
                    <div class="field has-addons"  style="display: flex;justify-content: space-around;">
                        <div class="control is-hidden-desktop">
                            <p class="is-size-7 is-hidden-desktop">検索するタグを選択してください&nbsp;</p>
                        </div>
                        <div class="control">
                            <div class="select is-multiple is-small">
                                <select multiple size="3" name="tags[]">
                                    <option value="">タグ検索を行わない</option>
                                    <?php foreach ($Tags as $Tag): ?>
                                        <?php
                                            $count=0;
                                            foreach ($selectTags as $selectTag):
                                                if($selectTag==$Tag->name){
                                                    $count++;
                                                }
                                            endforeach;
                                            if($count>0){
                                                echo "<option value=".h($Tag->name)." selected>".h($Tag->name)."</option>";
                                            }else{
                                                echo "<option value=".h($Tag->name).">".h($Tag->name)."</option>";
                                            }
                                        ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="control"style="width:280px;">
                        <?= $this->Form->text("keyword",['placeholder'=>'検索する商品名を入力してください。','class'=>'input is-small']); ?>
                    </div>
                    <div class="control" style="display: flex;justify-content: space-around;">
                        <?= $this->Form->button(__("検索"), ["type" => "submit",'class'=>'button is-small is-success has-text-weight-bold']); ?>
                    </div>
                <?= $this->Form->end(); ?>        
            </div>
            <div class="column"></div>
        </div>
        <div class="columns is-centered">
            <div class="item-list colimn is-flex-grow-1">
                <div class="item columns is-mobile is-multiline">
                    <?php foreach ($Items as $Item): ?>
                        <?php $tags = explode(",", $Item->tag_names);?>
                        <div class="column is-one-third-desktop is-half-tablet is-full-mobile">
                            <div class="card">
                                <div class="card-image">
                                  <figure class="image is-4by3">
                                    <?php echo "<img src=\"".$this->Url->build("/img/").h($Item->item_image)."\" alt=\"".h($Item->item_image)."\">" ?>
                                  </figure>
                                </div>
                                <div class="card-content">
                                  <div class="media">
                                    <div class="media-content">
                                      <p class="title is-4"><?= h($Item->item_name) ?></p></p>
                                    </div>
                                  </div>
                                  <div class="content">
                                    <?php foreach ($tags as $tag): ?>
                                        <?= $this->Html->link(__('#'.$tag),'https://konakera.sakura.ne.jp/items?tags%5B%5D='.$tag,['class'=>'is-small']) ?>
                                    <?php endforeach; ?>                                       
                                  </div>
                                </div>
                                <footer class="card-footer">
                                    <p class="card-footer-item">
                                        <span>
                                        <?= $this->Html->link(__('商品変更'), 'https://konakera.sakura.ne.jp/items/edit/'.$Item->item_id,['class'=>'button is-warning has-text-weight-bold']) ?>
                                        </span>
                                    </p>
                                    <p class="card-footer-item">
                                        <span>
                                            <?= $this->Html->link(__('商品削除'),'https://konakera.sakura.ne.jp/items/delete?id='.$Item->item_id, ['class'=>'button is-danger has-text-weight-bold','confirm' => __(' ID：{0} の商品を削除します。よろしいですか?', $Item->item_id)]) ?>
                                        </span>
                                    </p>           
                                </footer>
                            </div>
                        </div>
                    <?php endforeach; ?>                     
                </div>
            </div>
        </div>
        <?php if ($this->Paginator->param('count') > 6): ?>
            <nav class="pagination columns is-centered" style="margin-top:10px;">
                <?= $this->Paginator->next(__('次の商品情報を表示')) ?>
            </nav>
        <?php endif; ?>
    </div>
</section>