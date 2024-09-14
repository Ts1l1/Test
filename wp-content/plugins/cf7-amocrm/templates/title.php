<div class="wrap">
    <div>
        <a class="button button-order button-large" target="_blank" href="https://devamo.ru/wordpress/payurl?url=<?php echo $_SERVER['SERVER_NAME'] ?>">Оплатить подписку</a>
        <a class="button button-auth button-large" target="_blank" href="https://devamo.ru/wordpress/button">Авторизация в amoCRM</a>
        <?php if(isset($form)){ ?>
        <a class="button button-red button-large" href="/wp-admin/options-general.php?page=cf7_amocrm&form_id=<?= $form['ID'] ?>&clear=1">Обновить данные amoCRM</a>
        <?php }else{ ?>
        <a class="button button-red button-large" href="/wp-admin/options-general.php?page=cf7_amocrm&clear=1">Обновить данные amoCRM</a>
        <?php } ?>
        <a class="button button-default button-large" href="/wp-admin/options-general.php?page=cf7_amocrm&debug">Отладка</a>
        <a class="button button-blue button-large" href="/wp-admin/options-general.php?page=cf7_amocrm&changelog">История изменений</a>
    </div>
</div>
<div class="wrap">
    <h2 class="aci-title"><?= $title ?></h2>
</div>

<div class="wrap">
    <div class="alert_list">
        <?php if( isset($testing['auth']) and !$testing['auth'] ){ ?>
        <div class="alert">Требуется авторизация! Нажмите кнопку "Авторизация в amoCRM" и авторизуйте снова!</div>
        <?php } ?>

        <?php if( isset($testing['version']) and ($current_version !== $testing['version'])){ ?>
        <div class="alert">Ваша версия плагина устарела, рекомендуем <a target="_blank" href="https://devamo.ru/downloads/wordpress/cf7-amocrm.zip?v=<?= time() ?>">скачать новую версию</a> и обновить плагин.</div>
        <?php } ?>

        <?php if( isset($testing['subscribe']) and ( time() >= $testing['subscribe'] ) ){ ?>
        <div class="alert">Подписка на интеграцию истекла, для оплаты нажмите кнопку "Оплатить подписку".</div>
        <?php } ?>

        <?php if( isset($testing['subscribe']) and $days_left ){ ?>
        <div class="alert">Подписка на интеграцию истекает менее, чем через 10 дней! Рекомендуем оплатить подписку!</div>
        <?php } ?>

    </div>
</div>