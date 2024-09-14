<?php require_once (__DIR__ . '/title.php'); ?>
<form action="/wp-admin/options-general.php?page=cf7_amocrm" method="post">
    <input type="hidden" name="action" value="save_settings">
    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('cf7-amocrm-settings_page'); ?>">
    <div class="wrap">
        <div class="card" style="max-width:100%">

            <div>
                <h2 class="title">Логи</h2>
                <hr>

                <div class="row">
                    <?= $log ?>
                </div>

            </div>

		</div>
    </div>
</form>
<script>
    var pipelines = false;
    var pipeline_id = false;
    var pipeline_status = false;
</script>