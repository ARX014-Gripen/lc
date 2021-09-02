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
<!-- <div class="message success" onclick="this.classList.add('hidden')"><?= $message ?></div> -->
<article class="message is-success">
  <div class="message-header">
    <p>成功</p>
  </div>
  <div class="message-body">
      <?= $message ?>
  </div>
</article>