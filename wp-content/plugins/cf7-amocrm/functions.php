<?php 
function cf7_amocrm_process_complete( $form, $abort, $fields_data ) {
           
    $form_id = $_POST['_wpcf7'];
    $post_id = $_POST['_wpcf7_container_post'];

    $form_fields = get_post_meta( $form_id, 'cf7-amocrm', 1 );

    if( isset($form_fields['status']) and $form_fields['status'] ){

        $settings = get_option('cf7-amocrm');

        if(!isset($settings['ym_counter']) ){
            $settings['ym_counter'] = '';
        }

        $entityes = [];

        date_default_timezone_set('UTC');

        $options = get_option('cf7-amocrm-settings');
        $field_types = $options['field_types'];
        $utms = isset($options['utm_fields']) ? $options['utm_fields'] : false;
        $responsible_user_id = responsible_user_id_cf7_amocrm($form_id);
        $lead_name = $form_fields['lead_name'];
        $note = $form_fields['note'];
        $task_text = $form_fields['task']['task_text'];

        $page_name = get_the_title($post_id);
        $form_name = get_the_title($form_id);
        $page_url = get_option('home').'?p='.$post_id;
        $home_url = get_option('home');
        $current_datetime = date('Y-m-d H:i:s');
        $date = date('d.m.Y');
        $time = date('H:i:s');
    
        require_once (__DIR__ . '/fields_default.php');

        $ip = getIp_cf7_amocrm();
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        $os = getOS_cf7_amocrm($userAgent);
        $browser = getBrowser_cf7_amocrm($userAgent);
        $screen = isset($_COOKIE['screen']) ? $_COOKIE['screen'] : 'Не определен';

        foreach($custom_tags as $custom_tag => $custom_tag_name){
            $lead_name = str_replace('{{'.$custom_tag.'}}', ${$custom_tag}, $lead_name);
            $note = str_replace('{{'.$custom_tag.'}}', ${$custom_tag}, $note);
            $custom_tag_value = $form_fields['custom_tags'][$custom_tag]; // !!!!!!!!!
    
            if($custom_tag_value !== '0' and $custom_tag_value !== ''){
                $_data = explode('-', $custom_tag_value);
                $entity = $_data['0'];
                if( $entity === '' or $entity === null ){
                    continue;
                }
                $field_id = $_data['1'];
                $field_type = 'text';
                if(isset($field_types[$entity][$field_id])){
                    $field_type = $field_types[$entity][$field_id];
                }
    
                if( in_array($field_type, ['date_time', 'birthday', 'date']) ){
                    $form_value = strtotime(${$custom_tag});
                }elseif( in_array($field_type, ['checkbox']) ){
                    $form_value = ${$custom_tag} ? true : false;
                }else{
                    $form_value = (string)${$custom_tag};
                }
    
                if($form_value === '' or $form_value === null){
                    continue;
                }
                
                if($field_id === 'price'){
                    $form_value = (int)$form_value;
                }
    
                if( in_array($field_id, ['price', 'name', 'first_name', 'last_name']) ){
                    $entityes[$entity][$field_id] = $form_value;
                }else{
    
                    if(is_array(${$custom_tag})){
                        $values = [];
                        foreach(${$custom_tag} as $fv){
                            $values[] = ['value' => $fv];
                        }
                        $entityes[$entity]['custom_fields_values'][] = [
                            'field_id' => (int)$field_id,
                            'values' => $values
                        ];
                    }else{
                        $entityes[$entity]['custom_fields_values'][] = [
                            'field_id' => (int)$field_id,
                            'values' => [
                                [
                                    'value' => $form_value
                                ]
                            ]
                        ];
                    }
                }
            }
        }

        $form_data = $fields_data->get_posted_data();

        if( $attachments_data = $fields_data->uploaded_files() ){
            $names = array_keys($attachments_data);
            foreach($names as $name){
                $path = $attachments_data[$name][0];
                $wp_upload_dir = wp_upload_dir();
                $upload_dir = $wp_upload_dir['basedir'].'/cf7_amocrm/';
                if(!is_dir($upload_dir)){
                    mkdir($upload_dir, 0775, true);
                }
                $file_info = pathinfo($path);
                $new_file = $upload_dir.$file_info['basename'];
                if( !copy($path, $new_file) ){
                    unset($form_data[$name]);
                }else{
                    $form_data[$name] = $wp_upload_dir['baseurl'].'/cf7_amocrm/'.$file_info['basename'];
                    $entityes['files'][] = $wp_upload_dir['baseurl'].'/cf7_amocrm/'.$file_info['basename'];
                }
            }
        }

        foreach($form_data as $form_field => $form_field_value){
            $key = isset($form_fields['fields'][$form_field]) ? $form_fields['fields'][$form_field] : false;
            if($key !== false and $key != '0'){
                $_data = explode('-', $key);
                $entity = $_data['0'];
                if( $entity === '' or $entity === null ){
                    continue;
                }
                $field_id = $_data['1'];
    
                $field_type = 'text';
    
                if(isset($field_types[$entity][$field_id])){
                    $field_type = $field_types[$entity][$field_id];
                }

                if( in_array($field_type, ['date_time', 'birthday', 'date']) ){
                    $form_value = strtotime($form_field_value);
                }elseif( in_array($field_type, ['checkbox']) ){
                    $form_value = $form_field_value ? true : false;
                }else{
                    $form_value = (string)$form_field_value;
                }
    
                if($form_value === '' or $form_value === null){
                    continue;
                }

                if($field_id === 'price'){
                    $form_value = (int)$form_value;
                }
    
                if( in_array($field_id, ['price', 'name', 'first_name', 'last_name']) ){
                    $entityes[$entity][$field_id] = $form_value;
                }else{
                    if( in_array($field_type, ['checkbox']) ){
                        $entityes[$entity]['custom_fields_values'][] = [
                            'field_id' => (int)$field_id,
                            'values' => [
                                [
                                    'value' => $form_value
                                ]
                            ]
                        ];
                    }elseif(is_array($form_field_value)){
                        $values = [];
                        foreach($form_field_value as $fv){
                            $values[] = ['value' => $fv];
                        }
                        $entityes[$entity]['custom_fields_values'][] = [
                            'field_id' => (int)$field_id,
                            'values' => $values
                        ];
                    }else{
                        $entityes[$entity]['custom_fields_values'][] = [
                            'field_id' => (int)$field_id,
                            'values' => [
                                [
                                    'value' => $form_value
                                ]
                            ]
                        ];
                    }
                }
            }

            if(is_array($form_field_value)){
                $form_field_value = implode(', ', $form_field_value);
            }

            if( is_string($form_field_value)){
                $note = str_replace('{{'.$form_field.'}}', $form_field_value, $note);
                $lead_name = str_replace('{{'.$form_field.'}}', $form_field_value, $lead_name);     
                $task_text = str_replace('{{'.$form_field.'}}', $form_field_value, $task_text);

                if($form_fields['tags']){
                    foreach($form_fields['tags'] as $key => $tag){
                        $form_fields['tags'][$key] = str_replace('{{'.$form_field.'}}', $form_field_value, $tag);
                    }
                }

            }
        }
    
        if($utms){
            $ym_counter_field_id = 0;
            foreach($utms as $field_id => $utm){
                if (isset($_COOKIE[$utm])) {
                    $entityes['lead']['custom_fields_values'][] = [
                        'field_id' => (int)$field_id,
                        'values' => [
                            [
                                'value' => (string)$_COOKIE[$utm]
                            ]
                        ]
                    ];
                }
                if($utm === '_ym_counter'){
                    $ym_counter_field_id = $field_id;
                }
            }

            if( $ym_counter_field_id and $settings['ym_counter'] ){
                $entityes['lead']['custom_fields_values'][] = [
                    'field_id' => (int)$ym_counter_field_id,
                    'values' => [
                        [
                            'value' => (string)$settings['ym_counter']
                        ]
                    ]
                ];
            }

        }

        $entityes['task'] = [];
        if( $form_fields['task']['task_status'] == 1 ){
            $task_type = $form_fields['task']['task_type'];
            
            foreach($custom_tags as $custom_tag => $custom_tag_name){
                $task_text = str_replace('{{'.$custom_tag.'}}', ${$custom_tag}, $task_text);
            }

            $task_complete_till = $form_fields['task']['task_complete_till'];
            $task_complete_till_times = $form_fields['task']['task_complete_till_times'];
            
            $task_complete_till = strtotime($current_datetime . ' +' . $task_complete_till . ' ' . $task_complete_till_times );
            $entityes['task'] = [
                'responsible_user_id' => (int)$responsible_user_id,
                'task_type_id' => (int)$task_type,
                'text' => $task_text,
                'complete_till' => strtotime($current_datetime),
                'duration' => $task_complete_till - strtotime($current_datetime),
            ];
        }

        $pipeline_id = (int)$form_fields['pipeline_id'];
        $p_status = $form_fields['p_status'];
        $price = $form_fields['price'];

        $entityes['lead']['name'] = $lead_name;

        $unsortered = strpos($p_status, 'unsortered_');

        if($pipeline_id){
            $entityes['lead']['pipeline_id'] = $pipeline_id;
        }

        // если разобранное
        $entityes['lead']['status_id'] = (int)$p_status;
        $entityes['lead']['pipeline_id'] = $pipeline_id;       
        $entityes['lead']['responsible_user_id'] = (int)$responsible_user_id;

        if($price){
            $entityes['lead']['price'] = (int)$price;
        }           

        if($form_fields['tags']){
            foreach($form_fields['tags'] as $tag){
                if(empty($tag)){
                    continue;
                }
                foreach($custom_tags as $custom_tag => $custom_tag_name){
                    $tag = str_replace('{{'.$custom_tag.'}}', ${$custom_tag}, $tag);
                }
                $entityes['lead']['tags_to_add'][] = ['name' => (string)$tag];
            }
        }

        if(isset($entityes['contact'])){
            $entityes['contact']['responsible_user_id'] = (int)$responsible_user_id;
        }

        if(isset($entityes['company'])){
            $entityes['company']['responsible_user_id'] = (int)$responsible_user_id;
        }

        $entityes['note'] = $note;
        $entityes['ip'] = $ip;

        if(isset($form_fields['salesbot']) and (int)$form_fields['salesbot']['status'] and (int)$form_fields['salesbot']['id']){
            $entityes['salesbot'] = $form_fields['salesbot']['id'];
        }

        // неразобранное
        if($unsortered !== false){
            $entityes['lead']['request_id'] = (string)time();
            $entityes['lead']['source_name'] = get_option('blogname');
            $entityes['lead']['source_uid'] = md5(microtime(true).'devamo.ru');
            $entityes['lead']['_embedded']['metadata'] = [ 
                'ip' => $ip,
                'form_id' => $form_id,
                'form_sent_at' => (int)time(),
                'form_name' => $form_name,
                'form_page' => $page_url,
                'referer' => $_SERVER['HTTP_REFERER'],
                'category' => 'forms',
            ];
        }
        
        $data['action'] = 'CF7V3';      
        $data['data'] = json_encode($entityes);      
        $data['redirect'] = 'https://devamo.ru/wordpress/ajax';
        $data['url'] = $_SERVER['HTTP_HOST'];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL, 'https://proxy.amoapps.store/redirect.php');
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        $output = curl_exec($curl);
        curl_close($curl);

        $save_post_id = saveLead_cf7_amocrm($form_id, $entityes);
    } 
}
add_action( 'wpcf7_before_send_mail', 'cf7_amocrm_process_complete', 10, 4 );

function get_cf7form($form_id){
    $form = WPCF7_ContactForm::get_instance( $form_id );
    return $form->collect_mail_tags();
}

// сохраняет отправленные заявки
function saveLead_cf7_amocrm($form_id, $data){
    $form = get_post($form_id, 'ARRAY_A');
    $comment_count = $form['comment_count'];
    $comment_count++;
    $amo_settings = get_option('cf7-amocrm-settings');
    $text = [];
    foreach($data as $entity => $entity_value){
    
        $entity_keys = is_array($entity_value) ? array_keys($entity_value) : false;
        if($entity_keys){
            foreach($entity_keys as $entity_key){

                $value = $entity_value[$entity_key];
                $pipeline_id = isset($data[$entity]['pipeline_id']) ? $data[$entity]['pipeline_id'] : false;

                if( in_array($entity, ['lead', 'contact', 'company']) ){
                    switch ($entity_key) {
                        case 'name':
                            if($entity === 'lead'){
                                $name = 'Название сделки';
                            }elseif($entity === 'contact'){
                                $name = 'Название контакта';
                            }elseif($entity === 'company'){
                                $name = 'Название компании';
                            }
                            $text[$entity][0] = $name.': '.$value;
                            break;
                        case 'responsible_user_id':
                            $text[$entity][3] = 'Ответственный: '. (isset($amo_settings['managers'][$value]) ? $amo_settings['managers'][$value] : 'Не выбран');
                            break;
                        case 'pipeline_id':
                            $text[$entity][1] = 'Воронка: '.$amo_settings['pipelines'][$pipeline_id]['name'];
                            break;
                        case 'status_id':
                            if( isset($amo_settings['pipelines'][$pipeline_id]['statuses'][$value]['name']) ){
                                $text[$entity][2] = 'Этап воронки: '.$amo_settings['pipelines'][$pipeline_id]['statuses'][$value]['name'];
                            }
                            break;
                        case 'price':
                            $text[$entity][4] = 'Бюджет: '.$value;
                            break;
                        case 'first_name':
                            $text[$entity][0] = 'Имя: '.$value;
                            break;
                        case 'last_name':
                            $text[$entity][1] = 'Фамилия: '.$value;
                            break;
                        case 'tags_to_add':
                            if(isset($data['lead']['tags_to_add'])){
                                $tags = [];
                                foreach($data['lead']['tags_to_add'] as $tag){
                                    $tags[] = $tag['name'];
                                }
                                $text[$entity][99] = 'Теги: '.implode(', ', $tags);
                            }
                            break;
                        case 'custom_fields_values':
                            $fields = isset($data[$entity]['custom_fields_values']) ? $data[$entity]['custom_fields_values'] : false;
                            if($fields){
                                $i = 10;
                                foreach($fields as $field){
                                    $field_name = false;
                                    $i++;
                                    if(isset($amo_settings['fields'][$entity.'-'.$field['field_id']])){
                                        $field_name = $amo_settings['fields'][$entity.'-'.$field['field_id']];
                                        $field_name =  str_replace(['Контакт :: ', 'Сделка :: '], '', $field_name);
                                        if($field['values']){
                                            $field_values = [];
                                            foreach($field['values'] as $field_value){
                                                $field_values[] = $field_value['value'];
                                            }
                                            $text[$entity][$i] = $field_name.': '.implode(', ', $field_values);
                                        }
                                    }
        
                                    if(isset($amo_settings['utm_fields'][$field['field_id']])){
                                        $field_name = $amo_settings['utm_fields'][$field['field_id']];
                                        if($field['values']){
                                            $field_values = [];
                                            foreach($field['values'] as $field_value){
                                                $field_values[] = $field_value['value'];
                                            }
                                            $text[$entity][$i] = $field_name.': '.implode(', ', $field_values);
                                        }
                                    }
        
                                }
                            }
                            break;
                    }
                    }
                    
                    if($entity === 'task'){
                        $task = $data['task'];
                        switch ($entity_key) {
                            case 'responsible_user_id':
                                if( isset($amo_settings['managers'][$task['responsible_user_id']]) ){
                                    $text[$entity][3] = 'Ответственный: '.$amo_settings['managers'][$task['responsible_user_id']];
                                }
                                break;
                            case 'text':
                                $text[$entity][0] = 'Описание: '.$task['text'];
                                break;
                            case 'complete_till':
                                $text[$entity][1] = 'Дедлайн: '.date('d.m.Y H:i:s', $task['complete_till']);
                                break;
                            case 'task_type_id':
                                $task_type_name = '';
                                foreach($amo_settings['task_types'] as $task_type){
                                    if($task['task_type_id'] == $task_type['id']){
                                        $text[$entity][2] = 'Тип: '.$task_type['name'];
                                    }
                                }
                                break;
                        }
                    }
                }
        }

        if($entity === 'note'){
            $text[$entity] = $entity_value;
        }           
        
    }

    $print = '';

    if(isset($text['lead'])){
        ksort($text['lead']);
        $print .= '<div>';
        $print .= '<div><b>Сделка</b></div>';
        $print .= '<p>'.implode('</p><p>', $text['lead']).'</p>';
        $print .= '</div><br>';
    }

    if(isset($text['note'])){
        $print .= '<div>';
        $print .= '<div><b>Примечание</b></div>';
        $print .= '<p>' . str_replace(PHP_EOL, '</p><p>', $text['note']) . '</p>';
        $print .= '</div><br>';
    }

    if(isset($text['contact'])){
        ksort($text['contact']);
        $print .= '<div>';
        $print .= '<div><b>Контакт</b></div>';
        $print .= '<p>'.implode('</p><p>', $text['contact']).'</p>';
        $print .= '</div><br>';
    }

    if(isset($text['task'])){
        ksort($text['task']);
        $print .= '<div>';
        $print .= '<div><b>Задача</b></div>';
        $print .= '<p>'.implode('</p><p>', $text['task']).'</p>';
        $print .= '</div>';
    }
    
    return wp_insert_post(wp_slash([
        'post_title'    => date('d.m.Y H:i:s'),
        'post_parent'   => $form_id,
        'post_content'  => $print,
        'post_status'   => 'publish',
        'post_type'     => 'cf7_amocrm-lead',
        'post_excerpt' => json_encode($data)
    ]));
    
}

add_action( 'wp_ajax_exportcf7amocrm', 'export_cf7_amocrm' );
function export_cf7_amocrm(){
    $post = $_POST;
    if(!isset($post['lead_id'])){
        echo json_encode(['status' => 0, 'message' => 'Нет данных']);
        wp_die();
    }
    $lead = getLeadsByForm_cf7_amocrm($post['lead_id']);
    $data = [
        'data' => $lead->post_excerpt,
        'action' => 'CF7V2',
        'url' => $_SERVER['HTTP_HOST'],
        'redirect' => 'https://devamo.ru/wordpress/ajax',
    ];

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_URL, 'https://proxy.amoapps.store/redirect.php');
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
    $output = curl_exec($curl);
    curl_close($curl);
    $decode = json_decode($output, true);
    echo $output;
    wp_die();
}

function getLeadsCount_cf7_amocrm($form_id){
    global $wpdb;
    return $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->posts WHERE post_type = 'cf7_amocrm-lead' AND post_status = 'publish' AND post_parent = '".$form_id."'" );
}

function getLeadsByForm_cf7_amocrm($form_id){
    global $wpdb;
    return $wpdb->get_row( "SELECT * FROM $wpdb->posts WHERE post_type = 'cf7_amocrm-lead' AND post_status = 'publish' AND ID = '".$form_id."'" );
}

function getLeads_cf7_amocrm(){
    global $wpdb;
    $forms = $wpdb->get_results( "SELECT * FROM $wpdb->posts WHERE post_type = 'cf7_amocrm-lead' AND post_status = 'publish' ORDER BY ID DESC", 'ARRAY_A' );
    if($forms){
        foreach ( $forms as $key => $form ) {
            $forms[$key]['post_date'] = date('d.m.Y H:i', strtotime($form['post_date']));
        }
    }
    return $forms;
}

function get_cf7forms(){
    global $wpdb;
    $forms = $wpdb->get_results( "SELECT ID, post_title, post_date, post_content FROM $wpdb->posts WHERE post_type = 'wpcf7_contact_form' AND post_status = 'publish'", 'ARRAY_A' );
    foreach ( $forms as $key => $form ) {
        $forms[$key]['post_date'] = date('d.m.Y в H:i', strtotime($form['post_date']));
        $forms[$key]['fields'] = json_decode($form['post_content'], true) ? json_decode($form['post_content'], true)['fields'] : [];
        unset($forms[$key]['post_content']);
    }
    return $forms;
}

function getAmoFields_CF7($clear_cache = 0){
    $data = [
        'action' => 'getAmoCF', 
        'url' => $_SERVER['HTTP_HOST'], 
        'companies' => true, 
        'clear' => $clear_cache,
        'redirect' => 'https://devamo.ru/wordpress/ajax',
    ];
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_URL, 'https://proxy.amoapps.store/redirect.php');
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
    $output = curl_exec($curl);
    curl_close($curl);
    $output = json_decode($output, true);
	if(isset($output['status']) and $output['status']){
        update_option( 'cf7-amocrm-settings', $output['settings'] );
	}
	return isset($output['settings']) ? $output['settings'] : false;
}

function cf7_amocrm_admin() {
	add_options_page('Интеграция Contact Form 7 с amoCRM', 'Интеграция Contact Form 7 с amoCRM', 'manage_options', 'cf7_amocrm', 'cf7_amocrm_settings_page');
}

function cf7_amocrm_settings_page(){
	require_once (__DIR__ . '/admin.php');;
}

function cf7_save_utm_to_cookie() {
    $date = time()+3600*24*30;
    $options = get_option('cf7-amocrm-settings');
    $utms = isset($options['utm_fields']) ? $options['utm_fields'] : false;
    if($utms){
        foreach($utms as $utm){
            if(isset($_GET[$utm])) setcookie($utm, $_GET[$utm], $date, "/"); 
        }
    }
}

function cf7_amocrm_deactivate(){
    delete_option('cf7-amocrm');
    delete_option('cf7-amocrm-settings');
}

function cf7_amocrm_activate() {
    if (!function_exists('curl_version')) {
        deactivate_plugins(plugin_basename(__FILE__), true);
        wp_die('Curl не установлен! Для работы плагина требуется библиотека Curl, обратитесь в техническую поддержку хостинга или к вашему администратору сервера!');
    }

    if (!getAmoFields_CF7()) {
        deactivate_plugins(plugin_basename(__FILE__), true);
        wp_die('Сайт ' . $_SERVER['HTTP_HOST'] . ' не добавлен в настойках виджета amoCRM! <br>Перейдите в настройки виджета в amoCRM и добавьте свой сайт в соответвующее поле.<br>Техническая поддержка amo@devamo.ru или телеграм @bmite');
    }
}


add_filter('plugin_action_links', 'cf7_amocrm_links', 10, 2);
function cf7_amocrm_links($actions, $plugin_file ) {
	if( false === strpos( $plugin_file, 'cf7-amocrm' ) ){
        return $actions;
    }
	$settings_link = '<a href="/wp-admin/options-general.php?page=cf7_amocrm">Настройки</a>';
    $support_link = '<a href="https://t.me/bmite" target="_blanl">Поддержка</a>';
	array_unshift( $actions, $settings_link, $support_link ); 
	return $actions; 
}

function cf7_amocrm_scripts( $hook_suffix ) {
	if ( false === strpos( $hook_suffix, 'cf7_amocrm' ) ) {
		return;
	}
	wp_enqueue_script('cf7_amocrm', plugins_url( 'cf7-amocrm/js/main.js' ), '', filemtime( __DIR__ . ('/js/main.js') ) ,true);
	wp_enqueue_script('cf7_amocrm_chosen', plugins_url( 'cf7-amocrm/js/chosen.jquery.min.js' ), '', filemtime( __DIR__ . ('/js/chosen.jquery.min.js') ) ,true);
    wp_enqueue_style( 'cf7_amocrm_chosen', plugins_url( 'cf7-amocrm/css/chosen.min.css' ), null, filemtime( __DIR__ . ('/css/chosen.min.css') ), 'all' );
    wp_enqueue_style( 'cf7_amocrm', plugins_url( 'cf7-amocrm/css/main.css' ), null, filemtime( __DIR__ . ('/css/main.css') ), 'all' );

}

function cf7_amocrm_footer(){
    ?>
    <script>
    const cols = document.querySelectorAll('.wpcf7-form input[name=_wpcf7_container_post]');
    [].forEach.call(cols, (e)=>{
        e.value = <?php echo get_the_ID(); ?>;
    });
    document.cookie = 'screen=' + screen.width+'x'+screen.height;
    document.addEventListener('wpcf7beforesubmit', function(event) {
        var form = event.target;
        var submitButton = form.querySelector('.wpcf7-submit');
        if (submitButton) {
            submitButton.disabled = true;
            submitButton.classList.add('disabled');
        }
    }, false);

    document.addEventListener('wpcf7submit', function(event) {
        var form = event.target;
        var submitButton = form.querySelector('.wpcf7-submit');
        if (submitButton) {
            submitButton.disabled = false;
            submitButton.classList.remove('disabled');
        }
    }, false);

    </script>
    <?php
}

function testing(){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_URL, 'https://proxy.amoapps.store/redirect.php');
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query(['url' => $_SERVER['HTTP_HOST'], 'redirect' => 'https://devamo.ru/wordpress/version']));
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
    $output = curl_exec($curl);
    curl_close($curl);
    $output = json_decode($output, true);
    return $output;
}

// Добавляет ссылку в админ бар
add_action( 'admin_bar_menu', 'cf7_admin_bar_menu', 90 );
function cf7_admin_bar_menu( $wp_admin_bar ) {

	$wp_admin_bar->add_menu( array(
		'id'    => 'cf7_amocrm',
		'title' => 'Интеграция CF7 с amoCRM',
		'href'  => '/wp-admin/options-general.php?page=cf7_amocrm',
	) );

}

function delete_lead_cf7_amocrm(){
    $post = $_POST;
    if(!$post or !$post['leads_id']){
        echo json_encode(['status' => 0, 'message' => 'Нет данных']);
        wp_die();
    }
    $delete = [];
    foreach($post['leads_id'] as $lead_id){
        wp_delete_post($lead_id, true);
        $delete[] = $lead_id;
    }

    echo json_encode(['status' => 1, 'delete' => $delete]);
    wp_die();
}
add_action( 'wp_ajax_deleteleadcf7amocrm', 'delete_lead_cf7_amocrm' );

function responsible_user_id_cf7_amocrm($form_id){
    $form_fields = get_post_meta( $form_id, 'cf7-amocrm', 1 );
    if( !isset($form_fields['responsible_list']) ){
        return 0;
    }

    $distribution = (int)$form_fields['distribution'];
    $responsible_list = $form_fields['responsible_list'];
    $form_fields['current_responsible'] = isset($form_fields['current_responsible']) ? (int)$form_fields['current_responsible'] : 0;
    
    if($distribution){ // случайно
        $responsible_count = count($responsible_list) ? ( count($responsible_list) - 1) : 0;
        $form_fields['current_responsible'] = rand(0, $responsible_count);
    }else{
        $form_fields['current_responsible']++;
        $form_fields['current_responsible'] = isset($responsible_list[($form_fields['current_responsible'])]) ? ($form_fields['current_responsible']) : 0;
    }
    update_post_meta( $form_id, 'cf7-amocrm', $form_fields );
    return (int)$responsible_list[($form_fields['current_responsible'])];
}


function getIp_cf7_amocrm(){
    $client  = isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : false;
    $forward = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : false;
    $remote  = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : $_SERVER['REMOTE_ADDR'];
    if(filter_var($client, FILTER_VALIDATE_IP)){
        $ip = $client;
    }elseif(filter_var($forward, FILTER_VALIDATE_IP)){
        $ip = $forward;
    }else{
        $ip = $remote;
    }
    return $ip;
}

function getOS_cf7_amocrm($userAgent) { 
    $os_platform  = "Unknown OS Platform";
    $os_array     =  [
        '/windows nt 10/i'      =>  'Windows 10',
        '/windows nt 6.3/i'     =>  'Windows 8.1',
        '/windows nt 6.2/i'     =>  'Windows 8',
        '/windows nt 6.1/i'     =>  'Windows 7',
        '/windows nt 6.0/i'     =>  'Windows Vista',
        '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
        '/windows nt 5.1/i'     =>  'Windows XP',
        '/windows xp/i'         =>  'Windows XP',
        '/windows nt 5.0/i'     =>  'Windows 2000',
        '/windows me/i'         =>  'Windows ME',
        '/win98/i'              =>  'Windows 98',
        '/win95/i'              =>  'Windows 95',
        '/win16/i'              =>  'Windows 3.11',
        '/macintosh|mac os x/i' =>  'Mac OS X',
        '/mac_powerpc/i'        =>  'Mac OS 9',
        '/linux/i'              =>  'Linux',
        '/ubuntu/i'             =>  'Ubuntu',
        '/iphone/i'             =>  'iPhone',
        '/ipod/i'               =>  'iPod',
        '/ipad/i'               =>  'iPad',
        '/android/i'            =>  'Android',
        '/blackberry/i'         =>  'BlackBerry',
        '/webos/i'              =>  'Mobile'
    ];
    foreach ($os_array as $regex => $value){
        if (preg_match($regex, $userAgent)){
            $os_platform = $value;
        }
    }
    return $os_platform;
}

function getBrowser_cf7_amocrm($userAgent) {
      $browsers = [
        'Firefox',
        'Opera',
        'Chrome',
        'MSIE',
        'Safari'
      ];
      foreach($browsers as $browser){
          if(strpos($userAgent, $browser) !== false) { 
              return $browser;
          }
      }
      return 'Неизвестный'; // Хрен его знает, чего у него на десктопе стоит.
  }