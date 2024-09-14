<?php require_once (__DIR__ . '/title.php'); ?>

    <div class="wrap">
        <div class="card" style="max-width:100%">
            <h2 class="title">Все формы</h2>
            <hr>
            <div class="row">
                <table class="wp-list-table widefat fixed striped table-view-list cf7_amocrm forms_amocrm">

                    <thead>
                        <tr>
                            <th class="manage-column column-name column-primary desc">
                                <span>Имя</span>
                            </th>
                            <th id="shortcode" class="manage-column column-shortcode">Шорткод</th>
                            <th id="created" class="created column-created"><span>Создано</span></th>
                            <th class="created column-created">Кол-во заявок</th>
                        </tr>
                    </thead>

                    <tbody id="the-list">
                        <?php if($forms){ ?>
                        <?php foreach($forms as $form){ ?>
                        <tr>
                            <td class=" column-name has-row-actions column-primary">
                                <a href="/wp-admin/options-general.php?page=cf7_amocrm&form_id=<?= $form['ID'] ?>" title="Редактировать форму">
                                    <strong><?= $form['post_title'] ?></strong>
                                </a>
                            </td>
                            <td class="shortcode column-shortcode">[contact-form-7 id="<?= $form['ID'] ?>" title="<?= $form['post_title'] ?>"]</td>
                            <td class="created column-created"><?= $form['post_date'] ?></td>
                            <td class="created column-created"><?php echo getLeadsCount_cf7_amocrm($form['ID']); ?></td>
                        </tr>
                        <?php } ?>
                        <?php } ?>
                    </tbody>

                </table>

            </div>
        </div>

        <div class="card" style="max-width:100%">
            <h2 class="title">Мои заявки</h2>
            <hr>
            <div class="row">
                <table class="wp-list-table widefat fixed striped table-view-list forms_amocrm leads_list">
                    <thead>
                        <tr>
                            <th class="manage-column column-cb">
                                <input id="select-all" type="checkbox">
                                <label for="select-all" class="select-all"></label>
                            </th>
                            <th class="manage-column column-name column-primary desc">
                                <span>#</span>
                            </th>
                            <th id="shortcode" class="manage-column column-shortcode">Форма</th>
                            <th id="created" class="manage-column column-created desc">
                                <span>Создано</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($leads){ ?>
                            <?php foreach($leads as $lead){ ?>
                            <tr>
                                <td class="manage-column column-cb">
                                    <input class="select_lead" id="select-all-<?= $lead['ID'] ?>" type="checkbox" name="leads_id[]" value="<?= $lead['ID'] ?>">
                                    <label for="select-all-<?= $lead['ID'] ?>" class="select_lead_id"></label>
                                </td>
                                <td class=" column-name has-row-actions column-primary">
                                    <a href="/wp-admin/options-general.php?page=cf7_amocrm&lead_id=<?= $lead['ID'] ?>" title="Смотреть заявку">
                                        <strong>Заявка # <?= $lead['ID'] ?></strong>
                                    </a>
                                </td>
                                <td class="shortcode column-shortcode"><a href="/wp-admin/options-general.php?page=cf7_amocrm&form_id=<?= $lead['post_parent'] ?>" title="Настроить форму"><?= get_the_title($lead['post_parent']) ?></a></td>
                                <td class="created column-created"><?= $lead['post_date'] ?></td>
                            </tr>
                            <?php } ?>
                        <?php } ?>
                        <input type="hidden" name="action" value="deleteleadcf7amocrm">
                    </tbody>
                </table>
            </div>

            <div class="row">
                <div class="button action_delete_leads">Удалить выбранное</div>
            </div>
        </div>

        <form action="/wp-admin/options-general.php?page=cf7_amocrm" method="post">
            <input type="hidden" name="action" value="save_settings">
            <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('cf7-amocrm-settings_page'); ?>">

                <div class="card" style="max-width:100%">
                    <h2 class="title">Яндекс Метрика</h2>
                    <hr>
                    <div class="ym_counter">
                        <div class="row">
                            <i>Будет передаваться в поле сделки "_ym_counter" (вкладка статистика)</i>
                        </div>
                        <div class="row">
                            <div class="name">ID счетчика</div>
                            <div class="value"><input type="text" name="cf7-amocrm[ym_counter]" value="<?= $settings['ym_counter'] ?>"></div>
                        </div>
                    </div>
                </div>

                <br>
                <button type="submit" class="button button-primary button-large margin-left-0">Сохранить настройки</button>
        </form>
    </div>

<script>
var pipelines = false;
var pipeline_id = false;
var pipeline_status = false;
</script>
