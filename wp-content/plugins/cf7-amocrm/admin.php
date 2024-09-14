<?php 

    // сохраняем настройки формы
    if( isset($_POST['action']) and $_POST['action'] === 'save_form' and isset($_POST['nonce']) and wp_verify_nonce( $_POST['nonce'], 'cf7-amocrm-form') ){
        $_POST['cf7-amocrm']['tags'] = isset($_POST['cf7-amocrm']['tags']) ? $_POST['cf7-amocrm']['tags'] : [];
        update_post_meta( $_POST['form_id'], 'cf7-amocrm', ($_POST['cf7-amocrm']) );
    }

    // сохранем общие настройки плагина
    if( isset($_POST['action']) and $_POST['action'] === 'save_settings' and isset($_POST['nonce']) and wp_verify_nonce( $_POST['nonce'], 'cf7-amocrm-settings_page') ){
        update_option( 'cf7-amocrm', $_POST['cf7-amocrm'], 'no' );
    }

    // обновляем данные из amoCRM
    if( isset($_GET['clear']) and $_GET['clear']){
        getAmoFields_CF7(1);
    }

    require_once (__DIR__ . '/fields_default.php');
    $settings = get_option('cf7-amocrm');

    if( !isset($settings['ym_counter']) ){
        $settings['ym_counter'] = '';
    }

    $amo_settings = get_option('cf7-amocrm-settings');
    if( !$amo_settings ){
        $amo_settings = getAmoFields_CF7();
    }

    $amoFields = isset($amo_settings['fields']) ? $amo_settings['fields'] : ['0' => 'Поля не загружены'];
    $amoManagers = isset($amo_settings['managers']) ? $amo_settings['managers'] : ['0' => 'Менеджеры не загружены'];

    $testing = testing();
    $current_version = get_plugin_data(__DIR__ . '/cf7-amocrm.php')['Version'];
    $testing['subscribe'] = isset($testing['subscribe']) ? $testing['subscribe'] : time();
    $subscribe = isset($testing['subscribe']) ? 'до ' . date('d.m.Y', $testing['subscribe'] ) : 'Подписка на интеграцию истекла';
    $time_left = $testing['subscribe'] - time();
    $days_left = ($time_left < 864000 and $time_left > 0);

    if( isset($_GET['form_id']) and $_GET['form_id'] ){
        $get_post_meta = get_post_meta( $_GET['form_id'], 'cf7-amocrm', 1 );
        $fields = $get_post_meta ? ($get_post_meta) : [];
        $all_tags = array_keys(array_merge($custom_tags, ['country' => 'Страна', 'region' => 'Регион', 'city' => 'Город']));
        $all_tags[] = 'order_id';
        $form_tags = get_cf7form($_GET['form_id']);
        if($form_tags){
            foreach($form_tags as $form_field){
                $all_tags[] = $form_field;
            }
        }

        $form = get_post($_GET['form_id'], 'ARRAY_A');
        $amoFields = isset($amo_settings['fields']) ? $amo_settings['fields'] : [0 => 'Не загружено'];
        $amoManagers = isset($amo_settings['managers']) ? $amo_settings['managers'] : ['0' => 'Менеджеры не загружены'];
        $amoPipelines = isset($amo_settings['pipelines']) ? $amo_settings['pipelines'] : ['0' => ['name' => 'Воронки не загружены']];
        $fields['task'] = isset($fields['status']) ? $fields['task'] : $task_default;
        $task_types = $amo_settings['task_types'];
        $note = isset($fields['note']) ? $fields['note'] : $note_default;
        $fields['status'] = isset($fields['status']) ? $fields['status'] : true;
        $fields['salesbot'] = isset($fields['salesbot']) ? $fields['salesbot'] : ['status' => 0, 'id' => 0];
        $fields['lead_name'] = isset($fields['lead_name']) ? $fields['lead_name'] : $lead_name_default;
        $fields['price'] = isset($fields['price']) ? $fields['price'] : 1000;
        $fields['pipeline_id'] = (!isset($fields['pipeline_id']) and $amoPipelines ) ? key($amoPipelines) : $fields['pipeline_id']; 
        $fields['tags'] = isset($fields['tags']) ? $fields['tags'] : $tags_default;
        $fields['responsible_list'] = !isset($fields['responsible_list'])  ? [] : $fields['responsible_list'];
        $fields['distribution'] = isset($fields['distribution']) ? $fields['distribution'] : 0;
        $title = 'Настройка формы :: ' . $form['post_title'];
        $bots = $amo_settings['bots'];
        require_once (__DIR__ . '/templates/form_edit.php');
    }elseif( isset($_GET['lead_id']) and $_GET['lead_id'] ){
        $lead = get_post($_GET['lead_id'], 'ARRAY_A');
        $title = 'Заявка # '.$lead['ID'];
        require_once (__DIR__ . '/templates/lead_view.php');
    }elseif( isset($_GET['changelog']) ){
        $title = 'Лог изменений';
        require_once (__DIR__ . '/templates/changelog.php');
    }elseif( isset($_GET['debug']) ){
        $log = file_get_contents('https://devamo.ru/logs?widget=wordpress&url='.$_SERVER['SERVER_NAME']);
        $title = 'Отладка';
        require_once (__DIR__ . '/templates/debug.php');
    }else{
        $forms = get_cf7forms();
        $leads = getLeads_cf7_amocrm();
        $title = 'Общие настройки :: подписка активна до ' . date('d.m.Y', $testing['subscribe'] );
        require_once (__DIR__ . '/templates/settings_page.php');
    }
?>
