<?php require_once (__DIR__ . '/title.php'); ?>
<form action="/wp-admin/options-general.php?page=cf7_amocrm&form_id=<?= $form['ID'] ?>" method="post" id="amocrm_wpf">
    <input type="hidden" name="action" value="save_form">
    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('cf7-amocrm-form'); ?>">
    <input type="hidden" name="form_id" value="<?= $form['ID'] ?>">
    <div class="wrap">
        <div class="card" style="max-width:100%">
            <h2 class="title">Основное</h2>
            <hr>

            <table class="form-table">
		        <tbody>
                    <tr>
                        <th>Отправлять в amoCRM</th>
                        <td>
                            <select name="cf7-amocrm[status]">
                                <option value="1" <?php echo  $fields['status'] ? 'selected' : '' ?> >Да</option>
                                <option value="0" <?php echo !$fields['status'] ? 'selected' : '' ?> >Нет</option>
                            </select>
                        </td>
                        <td></td>
                    </tr>

                    <tr>
                        <th>Название сделки</th>
                        <td>
                            <input id="lead_name" type="text" name="cf7-amocrm[lead_name]" value="<?php echo $fields['lead_name'] ?>">
                        </td>
                        <td>
                            <div class="form_tags">
                                <span class="btn-xs btn-outline-primary"><?php echo implode('</span>, <span class="btn-xs btn-outline-primary">', $all_tags) ?></span>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <th>Список ответственных</th>
                        <td>
                            <select class="chosen-select"  name="cf7-amocrm[responsible_list][]" multiple data-placeholder="Выбрать">
                                <?php foreach($amoManagers as $manager_id => $manager){ ?>
                                <option value="<?php echo $manager_id ?>" <?php echo in_array($manager_id, $fields['responsible_list']) ? 'selected' : ''; ?>><?php echo $manager; ?></option>
                                <?php } ?>
                            </select>
                        </td>
                        <td></td>
                    </tr>

                    <tr>
                        <th>Распределять ответственных</th>
                        <td>
                            <select class="chosen-select"  name="cf7-amocrm[distribution]" data-placeholder="Выбрать">
                                <?php foreach($distributions as $distribution_id => $distribution_name){ ?>
                                <option value="<?php echo $distribution_id ?>" <?php echo ($distribution_id == $fields['distribution']) ? 'selected' : ''; ?>><?php echo $distribution_name; ?></option>
                                <?php } ?>
                            </select>
                        </td>
                        <td></td>
                    </tr>

                    <tr>
                        <th>Воронка</th>
                        <td>
                            <select id="pipeline_id" name="cf7-amocrm[pipeline_id]">
                                <?php foreach($amoPipelines as $pipeline_id => $pipeline){ ?>
                                    <option value="<?php echo $pipeline_id ?>" <?php echo $fields['pipeline_id'] == $pipeline_id ? 'selected' : ''; ?>><?php echo $pipeline['name']; ?></option>
                                <?php } ?>
                            </select>
                        </td>
                        <td></td>
                    </tr>

                    <tr>
                        <th>Этап воронки</th>
                        <td><select id="p_status" name="cf7-amocrm[p_status]"></select></td>
                        <td></td>
                    </tr>

                    <tr>
                        <th>Бюджет</th>
                        <td>
                            <input type="number" name="cf7-amocrm[price]" value="<?php echo $fields['price'] ?>" placeholder="Укажите бюджет сделки">
                        </td>
                        <td></td>
                    </tr>		
		        </tbody>
		    </table>

            <h2>Поля формы</h2>
            <hr>
            <table class="form-table">
		        <tbody>

                <?php if($form_tags){ ?>
                <?php foreach($form_tags as $form_field){ ?>
                    <tr>
                        <th><?= $form_field ?></th>
                        <td>
                            <select name="cf7-amocrm[fields][<?= $form_field ?>]" class="large-text">
                                <option value="0">Не выбрано</option>
                                <?php foreach($amoFields as $field_id => $amoField){ ?>
                                <option value="<?php echo $field_id ?>" <?= (isset($fields['fields'][$form_field]) and $field_id === $fields['fields'][$form_field]) ? 'selected' : '' ?>><?php echo $amoField ?></option>
                                <?php } ?>
                            </select>
                        </td>
                        <td></td>
                    </tr>
                <?php } ?>
                <?php } ?>
                                    
		        </tbody>  
		    </table>

            <h2>Системные поля</h2>
            <hr>
            <table class="form-table">
		        <tbody>

                <?php if($custom_tags){ ?>
                <?php foreach($custom_tags as $custom_tag => $custom_tag_name){ ?>

                    <tr>
                        <th><?= $custom_tag_name ?></th>
                        <td>
                            <select name="cf7-amocrm[custom_tags][<?= $custom_tag ?>]" class="large-text">
                                <option value="0" <?php echo ( isset($fields['custom_tags'][$custom_tag]) and $fields['custom_tags'][$custom_tag] === '0') ? 'selected' : ''; ?>>Не выбрано</option>
                                <?php foreach($amoFields as $field_id => $amoField){ ?>
                                <option value="<?php echo $field_id ?>" <?= (isset($fields['custom_tags'][$custom_tag]) and $field_id === $fields['custom_tags'][$custom_tag]) ? 'selected' : '' ?>><?php echo $amoField ?></option>
                                <?php } ?>
                            </select>
                        </td>
                        <td></td>
                    </tr>

                <?php } ?>
                <?php } ?>
                                    
		        </tbody>  
		    </table>

            <h2>Теги сделки</h2>
            <hr>
            <table class="form-table">
		        <tbody>
                <?php if(isset($fields['tags'])){ ?>
                <?php foreach($fields['tags'] as $tag){ ?>
                    <tr>
                        <th></th>
                        <td>
                            <input type="text" name="cf7-amocrm[tags][]" value="<?php echo $tag ?>">
                        </td>
                        <td>
                            <div class="button button-primary button-large removetag">Удалить</div>
                        </td>
                    </tr>
                <?php } ?>
                <?php } ?>
                    <tr>
                        <th></th>
                        <td></td>
                        <td>
                            <div class="button button-primary button-large newtag">Добавить</div>
                        </td>
                    </tr>
		        </tbody>  
		    </table>

            <h2>Задача сделки</h2>
            <hr>
            <table class="form-table">
		        <tbody>
                    <tr>
                        <th>Создавать задачу</th>
                        <td>
                            <select name="cf7-amocrm[task][task_status]" class="large-text">
                                <option value="1" <?php echo  $fields['task']['task_status'] ? 'selected' : ''; ?>>Да</option>
                                <option value="0" <?php echo !$fields['task']['task_status'] ? 'selected' : ''; ?>>Нет</option>
                            </select>
                        </td>
                        <td></td>
                    </tr>

                    <tr>
                        <th>Тип задачи</th>
                        <td>
                            <select name="cf7-amocrm[task][task_type]">
                            <?php foreach($task_types as $task_type){ ?>
                                <option value="<?php echo $task_type['id'] ?>" <?php echo $task_type['id'] == $fields['task']['task_type'] ? 'selected' : ''; ?>><?php echo $task_type['name']; ?></option>
                                <?php } ?>
                            </select>
                        </td>
                        <td></td>
                    </tr>

                    <tr>
                        <th>Описание задачи</th>
                        <td>
                            <input id="task_text" type="text" name="cf7-amocrm[task][task_text]" value="<?php echo $fields['task']['task_text'] ?>">
                        </td>
                        <td></td>
                    </tr>

                    <tr>
                        <th>Дедлайн задачи через</th>
                        <td>
                            <input id="task_complete_till" type="text" name="cf7-amocrm[task][task_complete_till]" value="<?php echo $fields['task']['task_complete_till'] ?>">
                        </td>
                        <td>
                            <select name="cf7-amocrm[task][task_complete_till_times]">
                            <?php foreach($times as $time => $time_ru){ ?>
                                <option value="<?php echo $time ?>" <?php echo  $time === $fields['task']['task_complete_till_times'] ? 'selected' : ''; ?>><?php echo $time_ru ?></option>
                            <?php } ?>
                            </select>
                        </td>
                    </tr>

		        </tbody>  
		    </table>

            <h2>Примечание сделки</h2>
            <hr>
            <table class="form-table">
		        <tbody>
                    <tr>
                        <th>Шаблон примечания</th>
                        <td>
                            <textarea id="note" name="cf7-amocrm[note]" rows="15"><?php echo $note ?></textarea>
                        </td>
                        <td>
                            <div class="form_tags">
                                <span class="btn-xs btn-outline-primary"><?php echo implode('</span>, <span class="btn-xs btn-outline-primary">', $all_tags) ?></span>
                            </div>
                        </td>
                    </tr>
		        </tbody>  
		    </table>

            <h2>Запуск сейлсбота</h2>
            <hr>
            <table class="form-table">
		        <tbody>
                    <tr>
                        <th>Статус</th>
                        <td>
                            <select name="cf7-amocrm[salesbot][status]">
                                <option value="1" <?php echo  $fields['salesbot']['status'] ? 'selected' : '' ?> >Включено</option>
                                <option value="0" <?php echo !$fields['salesbot']['status'] ? 'selected' : '' ?> >Отключено</option>
                            </select>
                        </td>
                        <td></td>
                    </tr>

                    <tr>
                        <th>Бот</th>
                        <td>
                            <select name="cf7-amocrm[salesbot][id]">
                                <option value="0" <?php echo ($fields['salesbot']['id'] == 0) ? 'selected' : ''; ?>>Не выбрано</option>
                                <?php if($bots){ ?>
                                    <?php foreach($bots as $bot_id => $bot_name){ ?>
                                        <option value="<?php echo $bot_id ?>" <?php echo ($fields['salesbot']['id'] == $bot_id) ? 'selected' : ''; ?>><?php echo $bot_name ?></option>
                                    <?php } ?>
                                <?php } ?>
                                
                            </select>
                        </td>
                        <td></td>
                    </tr>

		        </tbody>
		    </table>
            
        </div>

        <br>
        <button type="submit" class="button button-primary button-large margin-left-0">Сохранить настройки</button>

    </div>
</form>
<script>
    var pipelines = <?php echo json_encode($amoPipelines); ?>;
    var pipeline_id = <?php echo $fields['pipeline_id'] ? $fields['pipeline_id'] : 'false'; ?>;
    var pipeline_status = "<?php echo isset($fields['p_status']) ? $fields['p_status'] : 'false'; ?>";
</script>