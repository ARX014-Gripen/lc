<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User[]|\Cake\Collection\CollectionInterface $Users
 */
    $role = json_encode($role);
    $role_count = json_encode($role_count);
?>
<?php $this->assign('title', 'BI'); ?>
<?= $this->Html->css('//cdn.jsdelivr.net/chartist.js/latest/chartist.min.css') ?>
<?= $this->Html->script('//cdn.jsdelivr.net/chartist.js/latest/chartist.min.js') ?>
<?= $this->Html->script('https://cdn.jsdelivr.net/npm/chart.js@2.9.3/dist/Chart.min.js') ?>
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
                    <?= $this->Html->link("注文一覧", ['action' => 'index'],['class'=>'button is-success has-text-weight-bold']) ?>               
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
    <div class="columns is-centered  is-desktop">
        <div class="column">
            <h2 class="subtitle is-size-6-mobile is-size-4-tablet">ユーザの割合</h2>
            <canvas id="myChart1" style="position: relative; height:100; width:150"></canvas>
        </div>
        <div class="column">
            <h2 class="subtitle is-size-6-mobile is-size-4-tablet">注文数ランキング</h2>
            <?php
                $rank = 1;
                $cnt = 1;
                $bef_point = 0;
            ?>
            <?php foreach ($deliverer_ranking as $deliverer): ?>
                <?php
                    if($bef_point != (int)$deliverer->order_count){
                        $rank = $cnt;
                    }
                ?>
                <div style="padding-left: 35px;"><?= h($rank.'．') ?><?= mb_strimwidth( h($deliverer->deliverer_name), 0, 30, '…', 'UTF-8' ); ?></div>
                <?php
                    $bef_point = (int)$deliverer->order_count;
                    $cnt++;
                ?>
            <?php endforeach; ?>
            </ol>
        </div>
    </div>
</section>
<script>
    let role = JSON.parse('<?php echo $role; ?>');
    let role_count = JSON.parse('<?php echo $role_count; ?>');

    var ctx1 = $("#myChart1");

    // ドーナツグラフの場合
    var myDoughnutChart = new Chart(ctx1, {
        type: 'doughnut',
        data:{
            datasets:[{
                data:role_count,
                backgroundColor: ['darksalmon','skyblue','gold']
            }],
            labels: role
        },
        options: {
        }
    });


</script>
