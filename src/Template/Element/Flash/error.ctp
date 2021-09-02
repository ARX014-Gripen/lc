<?php
/**
 * @var \App\View\AppView $this
 * @var array $params
 * @var string $message
 */
if (!isset($params['escape']) || $params['escape'] !== false) {
    $message = h($message);
}
?>
<!-- <div class="message error" onclick="this.classList.add('hidden');"><?= $message ?></div> -->
<article class="message is-danger">
  <div class="message-header">
    <p>エラー</p>
  </div>
  <div class="message-body">
      <?= $message ?>
  </div>
</article>