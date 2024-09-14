<?php require_once (__DIR__ . '/title.php'); ?>
<form action="" method="post" id="amocrm_wpf">

    <div class="wrap">
        <div class="card" style="max-width:100%">
            <div class="lead_content">
                <?= $lead['post_content'] ?>
            </div>
        </div>
        <input type="hidden" name="action" value="exportcf7amocrm">
        <input type="hidden" name="lead_id" value="<?= $lead['ID'] ?>">
        <br>

        <div class="row">
            <button type="submit" id="export" class="button button-primary button-large margin-left-0">Экспортировать</button>
            <div class="result"></div>
        </div>

    </div>
</form>
<script>
    var pipeline_id = 0;
    var pipeline_status = 0;
</script>