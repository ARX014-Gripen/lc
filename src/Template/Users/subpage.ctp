<?php $this->assign('title', 'このアプリについて'); ?>
<?= $this->Html->script('burger') ?>
<?= $this->Html->script('pagetop') ?>
<?= $this->Html->css('pagetop') ?>
<?= $this->Html->css('subpage') ?>
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
                        "アプリへ",['action' => 'login'],['class' => 'button is-success has-text-weight-bold']
                    ) ?>                 
                </span>
            </div>
        </div>
    </div>
</section>
<p class="pagetop" style="display: block;">
    <a href="#">
        <i class="fas fa-chevron-up"></i>
    </a>
</p>
<section class="section">
    <h1 class="title is-size-5-mobile is-size-3-tablet">ウーバーイーツのような仕組みを作りたかった。</h1>
    <h2 class="subtitle is-size-6-mobile is-size-4-tablet">
        障害を持つ前にジオコーディングに関わる仕事をしていたため<br>
        ポートフォリオとして小さなウーバーイーツのような仕組みを作ってみました。
    </h2>
</section>
<section class="section">
    <h1 class="title is-size-5-mobile is-size-3-tablet">具体的にどんなアプリ？</h1>
    <h2 class="subtitle is-size-6-mobile is-size-4-tablet">
        注文した人に1番近い配達者が選ばれるアプリです。<br><br>
        注文者と配達者の住所から割り出したGPSの座標を元に、注文時に一番近い住所(座標)の配達者に配達が依頼されます。
    </h2>
</section>
<section class="section">
    <h1 class="title is-size-5-mobile is-size-3-tablet">どうやって使うの？</h1>
    <h2 class="subtitle is-size-6-mobile is-size-4-tablet">
        以下の順番で操作します。<br><br>
        <ol class="subpage-list">
            <li>1人分の注文者と2人分の配達者のアカウントを作成します。</li>
            <li>1人目の配達者でログインして、配達者情報を登録します。</li>
            <li>2人目の配達者でログインして、配達者情報を登録します。</li>
            <li>注文者でログインして、配達者情報を登録します。</li>
            <li>注文者でログインして、注文を行います。</li>
        </ol>
        <br>手順は以上です。使用を開始する前に以下のことに注意していただきたいです。<br><br>
        <ul class="subpage-list">
            <li>アカウント登録時のメールアドレスは架空のものを使用されると安全です。</li>
            <li>配達者と注文者の住所は、公共機関のものを使用されると安全です。</li>
            <li>配達者は登録されている全ての配達者から選ばれるため<br>登録時の想定とは違う配達者が選ばれることがあります
                。</li>
        </ul>
    </h2>
</section>
<section class="section">
    <h1 class="title is-size-5-mobile is-size-3-tablet">アカウントを削除したい</h1>
    <h2 class="subtitle is-size-6-mobile is-size-4-tablet">
        メールアドレスは「admin@hoge.jp」、パスワード「admin」でログイン<br>
        または、管理者アカウントを作成を行い、ログイン後に以下の手順で操作をお願います。<br><br>
        <ol class="subpage-list">
            <li>ユーザー一覧を選択</li>
            <li>ユーサー一覧から対象のアカウントを探す</li>
            <li>操作で削除を選択</li>
        </ol>
    </h2>
</section>
<section class="section">
    <h1 class="title is-size-5-mobile is-size-3-tablet">今後について</h1>
    <h2 class="subtitle is-size-6-mobile is-size-4-tablet">
        以下の機能を追加予定です。<br><br>
        <ul class="subpage-list">
            <li>注文内容が記載されたメールを登録メールアドレスに送信。</li>
            <li>管理者アカウントに注文全体の統計機能を追加。</li>
        </ul>
    </h2>
</section>
<section class="section">
    <h1 class="title is-size-5-mobile is-size-3-tablet">使用したフレームワーク/API</h1>
    <h2 class="subtitle is-size-6-mobile is-size-4-tablet">
        <ul class="subpage-list">
            <li>BULMA</li>
            <li>Geocoding</li>
            <li>GoogleMapURL</li>
        </ul>
    </h2>
</section>