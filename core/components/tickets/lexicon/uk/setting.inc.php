<?php
/**
 * Settings Russian Lexicon Entries
 */

$_lang['area_tickets.main'] = 'Основные';
$_lang['area_tickets.section'] = 'Раздел тикетов';
$_lang['area_tickets.ticket'] = 'Тикет';
$_lang['area_tickets.comment'] = 'Комментарий';
$_lang['area_tickets.mail'] = 'Почтовые уведомления';

$_lang['setting_tickets.frontend_css'] = 'Стили фронтенда';
$_lang['setting_tickets.frontend_css_desc'] = 'Путь к файлу со стилями магазина. Если вы хотите использовать собственные стили - укажите путь к ним здесь, или очистите параметр и загрузите их вручную через шаблон сайта.';
$_lang['setting_tickets.frontend_js'] = 'Скрипты фронтенда';
$_lang['setting_tickets.frontend_js_desc'] = 'Путь к файлу со скриптами магазина. Если вы хотите использовать собственные скрипты - укажите путь к ним здесь, или очистите параметр и загрузите их вручную через шаблон сайта.';

$_lang['setting_tickets.default_template'] = 'Шаблон для новых тикетов';
$_lang['setting_tickets.default_template_desc'] = 'Шаблон "по умолчанию" для новых тикетов. Используется и в административной части, и при создании тикета на фронтенде.';
$_lang['setting_tickets.ticket_isfolder_force'] = 'Все тикеты - контейнеры';
$_lang['setting_tickets.ticket_isfolder_force_desc'] = 'Обязательное указание параметра "isfolder" у тикетов';
$_lang['setting_tickets.ticket_hidemenu_force'] = 'Не показывать тикеты в меню';
$_lang['setting_tickets.ticket_hidemenu_force_desc'] = 'Обязательное указание параметра "hidemenu" у тикетов';
$_lang['setting_tickets.ticket_show_in_tree_default'] = 'Показывать в дереве по умолчанию';
$_lang['setting_tickets.ticket_show_in_tree_default_desc'] = 'Включите эту опцию, чтобы все создаваемые тикеты были видны в дереве ресурсов.';
$_lang['setting_tickets.section_content_default']  = 'Содержимое секций тикетов по умолчанию';
$_lang['setting_tickets.section_content_default_desc'] = 'Здесь вы можете указать контент вновь создаваемой секции тикетов. По умолчанию установен вывод дочерних тикетов.';

$_lang['setting_tickets.enable_editor'] = 'Редактор "markItUp"';
$_lang['setting_tickets.enable_editor_desc'] = 'Эта настройка активирует редактор "markItUp" на фронтенде, для удобной работы с тикетами и комментариями.';
$_lang['setting_tickets.editor_config.ticket'] = 'Настройки редактора тикетов';
$_lang['setting_tickets.editor_config.ticket_desc'] = 'Массив, закодированный в JSON для передачи в "markItUp". Подробности тут - http://markitup.jaysalvat.com/documentation/';
$_lang['setting_tickets.editor_config.comment'] = 'Настройки редактора комментариев';
$_lang['setting_tickets.editor_config.comment_desc'] = 'Массив, закодированный в JSON для передачи в "markItUp". Подробности тут - http://markitup.jaysalvat.com/documentation/';

$_lang['setting_tickets.disable_jevix_default'] = 'Отключать Jevix по умолчанию';
$_lang['setting_tickets.disable_jevix_default_desc'] = 'Эта настройка включает или отключает параметр "Отключить Jevix" по умолчанию у новых тикетов.';
$_lang['setting_tickets.process_tags_default'] = 'Выполнять теги по умолчанию';
$_lang['setting_tickets.process_tags_default_desc'] = 'Эта настройка включает или отключает параметр "Выполнять теги MODX" по умолчанию у новых тикетов.';
$_lang['setting_tickets.private_ticket_page'] = 'Редирект с приватных тикетов';
$_lang['setting_tickets.private_ticket_page_desc'] = 'Id существующего ресурса MODX, на который отправлять пользователя, если у него недостаточно прав для просмотра приватного тикета.';
$_lang['setting_tickets.unpublished_ticket_page'] = 'Страница неопубликованных тикетов';
$_lang['setting_tickets.unpublished_ticket_page_desc'] = 'Id существующего ресурса MODX, которая будет показана при запросе неопубликованного тикета.';
$_lang['setting_tickets.ticket_max_cut'] = 'Максимальный размер текста без сut';
$_lang['setting_tickets.ticket_max_cut_desc'] = 'Максимальное количество символов без тегов, которые можно сохранить без тега cut.';


$_lang['setting_tickets.snippet_prepare_comment'] = 'Сниппет обработки комментария';
$_lang['setting_tickets.snippet_prepare_comment_desc'] = 'Специальный сниппет, который будет обрабатывать комментарий. Перекрывает обработку по умолчанию и вызывается прямо в классе "Tickets", соответственно, ему доступны все методы и переменные этого класса.';
$_lang['setting_tickets.comment_edit_time'] = 'Время редактирования';
$_lang['setting_tickets.comment_edit_time_desc'] = 'Время в секундах, в течении которого можно редактировать свой комментарий.';
$_lang['setting_tickets.clear_cache_on_comment_save'] = 'Очищать кэш при комментировании';
$_lang['setting_tickets.clear_cache_on_comment_save_desc'] = 'Эта настройка включает очистку кэша тикета при действии с комментариями (создание\редактирование\удалении). Нужна только если вы вызываете сниппет "TicketComments" кэширвоанным.';

$_lang['setting_tickets.mail_from'] = 'Ящик исходящей почты';
$_lang['setting_tickets.mail_from_desc'] = 'Адрес для отправки почтовых уведомлений. Если не заполнен - будет использована настройка "emailsender".';
$_lang['setting_tickets.mail_from_name'] = 'Имя отправителя';
$_lang['setting_tickets.mail_from_name_desc'] = 'Имя, от которого будут отправлены все уведомления. Если не заполнен - будет использована настройка "site_name".';
$_lang['setting_tickets.mail_queue'] = 'Очередь сообщений';
$_lang['setting_tickets.mail_queue_desc'] = 'Нужно ли использовать очередь сообщений, или отправлять все письма сразу? Если вы активируете эту опцию, то вам нужно добавить в cron файл "/core/components/tickets/cron/mail_queue.php"';
$_lang['setting_tickets.mail_bcc'] = 'Уведомлять администраторов';
$_lang['setting_tickets.mail_bcc_desc'] = 'Укажите через запятую список <b>id</b> администраторов, которым нужно отправлять сообщения о новых тикетах и комментариях.';
$_lang['setting_tickets.mail_bcc_level'] = 'Уровень уведомления администраторов';
$_lang['setting_tickets.mail_bcc_level_desc'] = 'Возможны 3 уровня уведомлений администраторов: 0 - отключено, 1 - отправлять только сообщения о новых тикетах, 2 - тикеты + комментарии. Рекомендуемый уровень - 1.';

$_lang['setting_tickets.count_guests'] = 'Считать просмотры страниц гостями';
$_lang['setting_tickets.count_guests_desc'] = 'При включении этого параметра учитываются просмотры страниц всеми посетителями сайта, а не только авторизованными. Имейте в виду, что при таком подходе счетчик просмотров довольно легко накрутить.';

//$_lang['setting_tickets.section_id_as_alias'] = 'Id раздела как псевдоним';
//$_lang['setting_tickets.section_id_as_alias_desc'] = 'Если включено, псевдонимы для дружественных имён разделов не будут генерироваться. Вместо этого будут подставляться их id.';
//$_lang['setting_tickets.ticket_id_as_alias'] = 'Id тикета как псевдоним';
//$_lang['setting_tickets.ticket_id_as_alias_desc'] = 'Если включено, псевдонимы для дружественных имён тикетов не будут генерироваться. Вместо этого будут подставляться их id.';

$_lang['setting_tickets.source_default'] = 'Источник медиа для тикетов';
$_lang['setting_tickets.source_default_desc'] = 'Выберите источник медиа, который будет использован по умолчанию для загрузки файлов тикетов.';

$_lang['tickets.source_thumbnail_desc'] = 'Закодированный в JSON массив с параметрами генерации уменьшенной копии изображения.';
$_lang['tickets.source_maxUploadWidth_desc'] = 'Максимальная ширина изображения для загрузки. Всё, что больше, будет ужато до этого значения.';
$_lang['tickets.source_maxUploadHeight_desc'] = 'Максимальная высота изображения для загрузки. Всё, что больше, будет ужато до этого значения.';
$_lang['tickets.source_maxUploadSize_desc'] = 'Максимальный размер загружаемых изображений (в байтах).';
$_lang['tickets.source_imageNameType_desc'] = 'Этот параметр указывает, как нужно переименовать файл при загрузке. Hash - это генерация уникального имени, в зависимости от содержимого файла. Friendly - генерация имени по алгоритму дружественных url страниц сайта (они управляются системными настройками).';