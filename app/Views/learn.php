<div id="mui-learning-root" data-start="<?= (int)$cursor ?>" data-lesson-id="<?= (int)$lessonId ?>" data-token="<?= htmlspecialchars($token,ENT_QUOTES,'UTF-8') ?>">
  <div class="mui-loading" role="status">Darslar yuklanmoqda…</div>
</div>
<script type="application/json" id="word-data"><?= json_encode($words,JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP|JSON_UNESCAPED_UNICODE) ?></script>
<script type="application/json" id="lesson-data"><?= json_encode($allLessons,JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP|JSON_UNESCAPED_UNICODE) ?></script>
