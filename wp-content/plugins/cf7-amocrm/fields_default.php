<?php
$distributions = [
    '0' => 'По очереди',
    '1' => 'Случайно',
];

$custom_tags = [
    'page_name' => 'Название страницы',
    'form_name' => 'Название формы',
    'page_url' => 'Ссылка на страницу', 
    'home_url' => 'Ссылка на главную', 
    'date' => 'Текущая дата', 
    'time' => 'Текущее время',
    'ip' => 'IP',
    'os' => 'ОС',
    'browser' => 'Браузер',
    'screen' => 'Экран',
];

$task_default = [
    'task_status' => 1,
    'task_type' => 1,
    'task_text' => 'Дать оценку стоимости и перезвонить клиенту!',
    'task_complete_till' => 24,
    'task_complete_till_times' => 'hours'
];

$tags_default = ['Пример тега', '{{page_name}}'];

$times = ['seconds' => 'Секунд', 'minutes' => 'Минут', 'hours' => 'Часов', 'days' => 'Дней', 'weeks' => 'Недель', 'months' => 'Месяцев', 'years' => 'Лет'];  

$lead_name_default = 'Заявка #{{order_id}}, [{{page_name}}], [{{form_name}}]';

$note_default  = 'Название страницы: {{page_name}}' . PHP_EOL;
$note_default .= 'Название формы: {{form_name}}' . PHP_EOL;
$note_default .= 'Ссылка на страницу: {{page_url}}' . PHP_EOL;
$note_default .= 'Номер заказа: {{order_id}}' . PHP_EOL;
$note_default .= 'Главная страница: {{home_url}}' . PHP_EOL;
$note_default .= 'Дата и время: {{date}} {{time}}' . PHP_EOL;
$note_default .= 'IP: {{ip}}' . PHP_EOL;
$note_default .= 'ОС: {{os}}' . PHP_EOL;
$note_default .= 'Браузер: {{browser}}' . PHP_EOL;
$note_default .= 'Страна: {{country}}' . PHP_EOL;
$note_default .= 'Регион: {{region}}' . PHP_EOL;
$note_default .= 'Город: {{city}}' . PHP_EOL;
$note_default .= 'Экран: {{screen}}';