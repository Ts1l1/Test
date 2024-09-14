<?php
/**
 * Основные параметры WordPress.
 *
 * Скрипт для создания wp-config.php использует этот файл в процессе установки.
 * Необязательно использовать веб-интерфейс, можно скопировать файл в "wp-config.php"
 * и заполнить значения вручную.
 *
 * Этот файл содержит следующие параметры:
 *
 * * Настройки базы данных
 * * Секретные ключи
 * * Префикс таблиц базы данных
 * * ABSPATH
 *
 * @link https://ru.wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Параметры базы данных: Эту информацию можно получить у вашего хостинг-провайдера ** //
/** Имя базы данных для WordPress */
define( 'DB_NAME', 'tsikarfd_1' );

/** Имя пользователя базы данных */
define( 'DB_USER', 'tsikarfd_1' );

/** Пароль к базе данных */
define( 'DB_PASSWORD', 'Leyla1' );

/** Имя сервера базы данных */
define( 'DB_HOST', 'localhost' );

/** Кодировка базы данных для создания таблиц. */
define( 'DB_CHARSET', 'utf8mb4' );

/** Схема сопоставления. Не меняйте, если не уверены. */
define( 'DB_COLLATE', '' );

/**#@+
 * Уникальные ключи и соли для аутентификации.
 *
 * Смените значение каждой константы на уникальную фразу. Можно сгенерировать их с помощью
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ сервиса ключей на WordPress.org}.
 *
 * Можно изменить их, чтобы сделать существующие файлы cookies недействительными.
 * Пользователям потребуется авторизоваться снова.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'CEV7kTx*r!ib6bwJg>VdN3upAlbG5#`u/EJ2<q<}y6h$4TU77EH:D<[lS8B0;iEg' );
define( 'SECURE_AUTH_KEY',  '>JO]QFhB0O=}?}fEo}_Lmum)c`l`&x14iA=E}(4)hS,_Gj%*vTC(^uO=UT*QG/?p' );
define( 'LOGGED_IN_KEY',    'O#VTC8lhh[OL!7=ze#8zS}J@q;6gh-D-5CDFSgc0I+fi>9@>bp^D3P$Szalzx[u]' );
define( 'NONCE_KEY',        'I)wk<p|hu1#E?~[)[V>9>EhdCG 7V/u9H)QV)^b4]yFVx~>QB~H0E[[n%>b]~q>M' );
define( 'AUTH_SALT',        'H1GCDW;[Ab7-O?}!fdwq2QE#5nqy](C83f36xH9]^}!qDwZi1}a8o&,!EM)mL~8U' );
define( 'SECURE_AUTH_SALT', '_FU!$40u*eGQ/IT1rJs!+^r2pyzmcyqPHkVPJm|i!F#UiQWC&,!=y/?`Q`w!RgR8' );
define( 'LOGGED_IN_SALT',   'af$oV:c6)uSYIk5XKAK6o:Gz]kM#<r%SF>NE8]d5|.3tNnwr|ao!]vk{e%wwwJH^' );
define( 'NONCE_SALT',       '].^S}VS;#^a94$LQNl*~H#prxx3Doa:wzY4PAJbs.i)jiMhx/xe6DQW~KAOYJ}sT' );

/**#@-*/

/**
 * Префикс таблиц в базе данных WordPress.
 *
 * Можно установить несколько сайтов в одну базу данных, если использовать
 * разные префиксы. Пожалуйста, указывайте только цифры, буквы и знак подчеркивания.
 */
$table_prefix = 'wp_';

/**
 * Для разработчиков: Режим отладки WordPress.
 *
 * Измените это значение на true, чтобы включить отображение уведомлений при разработке.
 * Разработчикам плагинов и тем настоятельно рекомендуется использовать WP_DEBUG
 * в своём рабочем окружении.
 *
 * Информацию о других отладочных константах можно найти в документации.
 *
 * @link https://ru.wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Произвольные значения добавляйте между этой строкой и надписью "дальше не редактируем". */



/* Это всё, дальше не редактируем. Успехов! */

/** Абсолютный путь к директории WordPress. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Инициализирует переменные WordPress и подключает файлы. */
require_once ABSPATH . 'wp-settings.php';
