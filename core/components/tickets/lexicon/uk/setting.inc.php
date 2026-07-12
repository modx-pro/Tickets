<?php
/**
 * Settings Ukrainian Lexicon Entries
 */

$_lang['area_tickets.main'] = 'Основні';
$_lang['area_tickets.section'] = 'Розділ тікетів';
$_lang['area_tickets.ticket'] = 'Тікет';
$_lang['area_tickets.comment'] = 'Коментар';
$_lang['area_tickets.mail'] = 'Поштові сповіщення';

$_lang['setting_tickets.frontend_css'] = 'Стилі фронтенду';
$_lang['setting_tickets.frontend_css_desc'] = 'Шлях до CSS-файлу фронтенду. Вкажіть свій шлях або очистіть параметр і підключіть стилі в шаблоні сайту.';
$_lang['setting_tickets.frontend_js'] = 'Скрипти фронтенду';
$_lang['setting_tickets.frontend_js_desc'] = 'Шлях до JS-файлу фронтенду. Вкажіть свій шлях або очистіть параметр і підключіть скрипти в шаблоні сайту.';

$_lang['setting_tickets.date_format'] = 'Формат дати';
$_lang['setting_tickets.date_format_desc'] = 'Формат дати в оформленні тікетів.';
$_lang['setting_tickets.default_template'] = 'Шаблон для нових тікетів';
$_lang['setting_tickets.default_template_desc'] = 'Шаблон за замовчуванням для нових тікетів. Використовується в адмінці й під час створення тікета на сайті.';
$_lang['setting_tickets.ticket_isfolder_force'] = 'Усі тікети як контейнери';
$_lang['setting_tickets.ticket_isfolder_force_desc'] = 'Примусово вмикає параметр isfolder для тікетів.';
$_lang['setting_tickets.ticket_hidemenu_force'] = 'Не показувати тікети в меню';
$_lang['setting_tickets.ticket_hidemenu_force_desc'] = 'Примусово вмикає параметр hidemenu для тікетів.';
$_lang['setting_tickets.ticket_show_in_tree_default'] = 'Показувати в дереві за замовчуванням';
$_lang['setting_tickets.ticket_show_in_tree_default_desc'] = 'Якщо увімкнено, нові тікети з’являються в дереві ресурсів.';
$_lang['setting_tickets.section_content_default'] = 'Вміст розділу тікетів за замовчуванням';
$_lang['setting_tickets.section_content_default_desc'] = 'Вміст нового розділу тікетів. За замовчуванням виводить дочірні тікети.';

$_lang['setting_tickets.enable_editor'] = 'Редактор markItUp';
$_lang['setting_tickets.enable_editor_desc'] = 'Умикає markItUp на фронтенді для тікетів і коментарів.';
$_lang['setting_tickets.editor_config.ticket'] = 'Налаштування редактора тікетів';
$_lang['setting_tickets.editor_config.ticket_desc'] = 'JSON-масив налаштувань для markItUp. Документація: http://markitup.jaysalvat.com/documentation/';
$_lang['setting_tickets.editor_config.comment'] = 'Налаштування редактора коментарів';
$_lang['setting_tickets.editor_config.comment_desc'] = 'JSON-масив налаштувань для markItUp. Документація: http://markitup.jaysalvat.com/documentation/';

$_lang['setting_tickets.disable_jevix_default'] = 'Вимикати Jevix за замовчуванням';
$_lang['setting_tickets.disable_jevix_default_desc'] = 'Значення параметра «Вимкнути Jevix» для нових тікетів.';
$_lang['setting_tickets.process_tags_default'] = 'Виконувати теги за замовчуванням';
$_lang['setting_tickets.process_tags_default_desc'] = 'Значення параметра «Виконувати теги MODX» для нових тікетів.';
$_lang['setting_tickets.private_ticket_page'] = 'Редирект із приватних тікетів';
$_lang['setting_tickets.private_ticket_page_desc'] = 'Id ресурсу MODX, куди направити користувача без прав на перегляд приватного тікета.';
$_lang['setting_tickets.unpublished_ticket_page'] = 'Сторінка неопублікованих тікетів';
$_lang['setting_tickets.unpublished_ticket_page_desc'] = 'Id ресурсу MODX, який показується для неопублікованого тікета.';
$_lang['setting_tickets.ticket_max_cut'] = 'Максимальний розмір тексту без cut';
$_lang['setting_tickets.ticket_max_cut_desc'] = 'Максимальна кількість символів без тегів, яку можна зберегти без тега &lt;cut/&gt;.';

$_lang['setting_tickets.snippet_prepare_comment'] = 'Сніпет обробки коментаря';
$_lang['setting_tickets.snippet_prepare_comment_desc'] = 'Сніпет, який обробляє коментар перед виводом. Викликається з класу Tickets і має доступ до його методів і змінних.';
$_lang['setting_tickets.comment_edit_time'] = 'Час редагування';
$_lang['setting_tickets.comment_edit_time_desc'] = 'Скільки секунд можна редагувати власний коментар.';
$_lang['setting_tickets.clear_cache_on_comment_save'] = 'Очищати кеш під час коментування';
$_lang['setting_tickets.clear_cache_on_comment_save_desc'] = 'Очищає кеш тікета під час створення, зміни чи видалення коментаря. Потрібно, якщо викликаєте TicketComments з кешем.';

$_lang['setting_tickets.mail_from'] = 'Адреса відправника';
$_lang['setting_tickets.mail_from_desc'] = 'Email для сповіщень. Якщо порожньо, береться системна настройка emailsender.';
$_lang['setting_tickets.mail_from_name'] = 'Ім’я відправника';
$_lang['setting_tickets.mail_from_name_desc'] = 'Ім’я в листах. Якщо порожньо, береться системна настройка site_name.';
$_lang['setting_tickets.mail_queue'] = 'Черга листів';
$_lang['setting_tickets.mail_queue_desc'] = 'Надсилати через чергу чи одразу. Для черги додайте в cron файл /core/components/tickets/cron/mail_queue.php';
$_lang['setting_tickets.mail_bcc'] = 'Сповіщати адміністраторів';
$_lang['setting_tickets.mail_bcc_desc'] = 'Список id адміністраторів через кому, яким надсилати листи про нові тікети й коментарі.';
$_lang['setting_tickets.mail_bcc_level'] = 'Рівень сповіщень адміністраторів';
$_lang['setting_tickets.mail_bcc_level_desc'] = '0 — вимкнено, 1 — лише нові тікети, 2 — тікети й коментарі. Рекомендовано 2. За замовчуванням BCC — id 1 (адмін).';

$_lang['setting_tickets.count_guests'] = 'Рахувати перегляди гостями';
$_lang['setting_tickets.count_guests_desc'] = 'Рахує перегляди всіх відвідувачів, не лише авторизованих. Лічильник тоді легше накрутити.';

$_lang['setting_tickets.max_files_upload'] = 'Ліміт завантажуваних файлів';
$_lang['setting_tickets.max_files_upload_desc'] = 'Скільки файлів користувач може прикріпити до тікета. 0 — без обмежень.';

//$_lang['setting_tickets.section_id_as_alias'] = 'Id розділу як псевдонім';
//$_lang['setting_tickets.section_id_as_alias_desc'] = 'Якщо увімкнено, псевдоніми розділів не генеруються; підставляється id.';
//$_lang['setting_tickets.ticket_id_as_alias'] = 'Id тікета як псевдонім';
//$_lang['setting_tickets.ticket_id_as_alias_desc'] = 'Якщо увімкнено, псевдоніми тікетів не генеруються; підставляється id.';

$_lang['setting_mgr_tree_icon_ticket'] = 'Іконка тікета';
$_lang['setting_mgr_tree_icon_ticket_desc'] = 'Іконка тікета в дереві ресурсів.';
$_lang['setting_mgr_tree_icon_ticketssection'] = 'Іконка розділу тікетів';
$_lang['setting_mgr_tree_icon_ticketssection_desc'] = 'Іконка розділу тікетів у дереві ресурсів.';

$_lang['setting_tickets.source_default'] = 'Медіаджерело для тікетів';
$_lang['setting_tickets.source_default_desc'] = 'Медіаджерело за замовчуванням для файлів тікетів.';

$_lang['tickets.source_thumbnails_desc'] = 'JSON-масив параметрів генерації мініатюри.';
$_lang['tickets.source_maxUploadWidth_desc'] = 'Максимальна ширина зображення. Більші файли зменшуються до цього значення.';
$_lang['tickets.source_maxUploadHeight_desc'] = 'Максимальна висота зображення. Більші файли зменшуються до цього значення.';
$_lang['tickets.source_maxUploadSize_desc'] = 'Максимальний розмір файлу (у байтах).';
$_lang['tickets.source_imageNameType_desc'] = 'Як перейменовувати файл після завантаження. Hash — унікальне ім’я за вмістом. Friendly — за правилами дружніх URL (системні настройки).';

$_lang['setting_tickets.auto_introtext'] = 'Автозаповнення анотації тікета';
$_lang['setting_tickets.auto_introtext_desc'] = 'Якщо увімкнено, порожня анотація заповнюється з контенту тікета (текст до &lt;cut/&gt; або весь контент).';
