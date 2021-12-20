<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Orderer $orderer
 */
    $satisfactions = json_encode($satisfactions);
    $delivery_datetimes = json_encode($delivery_datetimes);
?>
<?php $this->assign('title', '商品詳細'); ?>
<?php 
   // ワンタイムチケットを生成する。
    $ticket = md5(uniqid(rand(), true));
    $session = $this->getRequest()->getSession();
    $session->write('ticket',$ticket);
?>
<?= $this->Html->css('//cdn.jsdelivr.net/chartist.js/latest/chartist.min.css') ?>
<?= $this->Html->script('//cdn.jsdelivr.net/chartist.js/latest/chartist.min.js') ?>
<?= $this->Html->script('https://cdn.jsdelivr.net/npm/chart.js@2.9.3/dist/Chart.min.js') ?>
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
            <div class="field">
              <label class="label is-size-5">商品詳細</label>
            </div>
            <div class="field">
              <label class="label">商品名</label>
              <div class="control">
                <?= h($Item->name) ?>
              </div>
            </div>
            <div class="field">
              <div class="card">
                <div class="card-image">
                  <figure class="image is-4by3">
                  <?php echo "<img src=\"".$this->Url->build("/img/").h($Item->image)."\" alt=\"".h($Item->image)."\">" ?>
                  </figure>
                </div>
              </div>
            </div>
            <div class="field">
              <label for="" class="label">タグ</label>
              <div class="control">
                <div class="select is-multiple">
                    <?php foreach ($Tags as $tag): ?>
                        <?= $this->Html->link(__('#'.$tag),'https://konakera.sakura.ne.jp/items?tags%5B%5D='.$tag,['class'=>'is-small']) ?>
                    <?php endforeach; ?>  
                </div>
              </div>
            </div>
            <div class="field">
                <label for="" class="label">満足度</label>
                <canvas id="myChart1" style="position: relative; height:100; width:150"></canvas>
            </div>
        </div>
    </div>
</section>
<script>
    let satisfactions = JSON.parse('<?php echo $satisfactions; ?>');
    let delivery_datetimes = JSON.parse('<?php echo $delivery_datetimes; ?>');

    var ctx1 = $("#myChart1");
// array(6) { [0]=> int(5) [1]=> int(4) [2]=> int(1) [3]=> int(3) [4]=> int(1) [5]=> int(5) }
    // ドーナツグラフ
    var myDoughnutChart = new Chart(ctx1, {
        type: 'line',
        data:{
            labels:delivery_datetimes,
            datasets:[{
                data:satisfactions,
                borderColor: 'rgba(255, 100, 100, 1)',
                lineTension: 0,
                fill: false,
                borderWidth: 3,
                label:'満足度'
            }],
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        max: 5,
                        min: 1,
                        stepSize: 1,
                    }
                }]
            }
        }
    });

</script>
