<?php
/**
 *  Ukrainian Lexicon Entries for Tickets (by viktorminator)
 */

include_once('setting.inc.php');

$_lang['tickets'] = 'Тікети';
$_lang['comments'] = 'Коментарі';
$_lang['threads'] = 'Гілки коментарів';
$_lang['tickets_section'] = 'Розділ';
$_lang['ticket'] = 'Тікет';
$_lang['ticket_all'] = 'Все';
$_lang['ticket_menu_desc'] = 'Управління коментарями і не тільки.';
$_lang['comments_all'] = 'Всі коментарі';

$_lang['tickets_section_create_here'] = 'Розділ з тікетами';
$_lang['tickets_section_new'] = 'Новий розділ тікетів';

$_lang['ticket_create_here'] = 'Створити тікет';

$_lang['ticket_no_comments'] = 'На цій сторінці ще немає коментарів. Ви можете написати перший.';
$_lang['err_no_jevix'] = 'Для роботи необхідний Jevix сніпет. Ви повинні встановити його з репозіторію MODX.';
$_lang['tickets_err_unknown'] = 'Відбулася невідома помилка.';
$_lang['tickets_message_close_all'] = 'закрити все';
$_lang['ticket_err_wrong_user'] = 'Ви намагаєтесь оновити тікет, що вам не належить.';
$_lang['ticket_err_no_auth'] = 'Ви повинні авторизуватись, щоб створити тікет.';
$_lang['ticket_err_wrong_parent'] = 'Вказано невірний розділ для тікета.';
$_lang['ticket_err_wrong_resource'] = 'Вказано невірний тікет.';
$_lang['ticket_err_wrong_thread'] = 'Вказана неправильна гілка коментарів.';
$_lang['ticket_err_access_denied'] = 'Доступ заборонено.';
$_lang['ticket_err_form'] = 'Форма містить помилки. Будьласка виправте їх.';
$_lang['ticket_err_deleted_comment'] = 'Ви намагаєтесь редагувати видалений коментар.';
$_lang['ticket_err_unpublished_comment'] = 'Цей коментар ще не було опубліковано.';
$_lang['ticket_err_ticket'] = 'Вказаний тікет не існує.';
$_lang['ticket_err_vote_own'] = 'Ви не можете голосувати за свій тікет.';
$_lang['ticket_err_vote_already'] = 'Ви вже голосували за цей тікет.';
$_lang['ticket_err_empty'] = 'Ви забули написати текст тікета.';
$_lang['ticket_unpublished_comment'] = 'Ваш комментар буде опубліковано після перевірки.';
$_lang['permission_denied'] = 'У вас недостатньо прав для цієї дії.';
$_lang['field_required'] = 'Це поле обовязкове.';
$_lang['ticket_clear'] = 'Очистити';

$_lang['ticket_comment_intro'] = '';
$_lang['ticket_comment_all_intro'] = 'Тут зібрані коментарі з усього сайту.';
$_lang['ticket_comment_email_owner'] = 'Новий коментар до вашого тікету "[[+pagetitle]]"';
$_lang['ticket_comment_email_reply'] = 'Відповідь на ваш коментар до тікету "[[+pagetitle]]"';
$_lang['ticket_comment_deleted_text'] = 'Коментар видалено.';
$_lang['ticket_comment_update'] = 'Змінити коментар';
$_lang['ticket_comment_remove'] = 'Видалити разом з нащадками';
$_lang['ticket_comment_remove_confirm'] = 'Ви впевнені, що хочете остаточно видалити <b>гілку коментарів</b>, починаючи з цієї? Ця операція незворотня!';
$_lang['ticket_comment_delete'] = 'Видалити коментар';
$_lang['ticket_comment_undelete'] = 'Відновити коментар';
$_lang['ticket_comment_publish'] = 'Публікувати коментар';
$_lang['ticket_comment_unpublish'] = 'Зняти з публікації';
$_lang['ticket_comment_viewauthor'] = 'Відкрити сторінку автора';

$_lang['ticket_comment_name'] = 'Автор';
$_lang['ticket_comment_text'] = 'Коментар';
$_lang['ticket_comment_createdon'] = 'Написаний';
$_lang['ticket_comment_editedon'] = 'Змінений';
$_lang['ticket_comment_deletedon'] = 'Видалений';
$_lang['ticket_comment_parent'] = 'Батько';
$_lang['ticket_comment_email'] = 'Email';
$_lang['ticket_comment_view'] = 'Відкрити коментар на сайті';
$_lang['ticket_comment_reply'] = 'відповісти';
$_lang['ticket_comment_edit'] = 'змінити';
$_lang['ticket_comment_create'] = 'Написати коментар';
$_lang['ticket_comment_preview'] = 'Прогляд';
$_lang['ticket_comment_save'] = 'Написати';
$_lang['ticket_comment_was_edited'] = 'Коментар було змінено';
$_lang['ticket_comment_guest'] = 'Гість';
$_lang['ticket_comment_deleted'] = 'Видалено';
$_lang['ticket_comment_captcha'] = 'Введіть суму [[+a]] + [[+b]]';
$_lang['ticket_comment_err_no_auth'] = 'Ви маєте авторизуватись, щоб залишити коментар.';
$_lang['ticket_comment_err_wrong_user'] = 'Ви намагаєтесь оновити коментар, який вам не належить.';
$_lang['ticket_comment_err_no_time'] = 'Час для редагування минув.';
$_lang['ticket_comment_err_has_replies'] = 'У цього коментаря є вже відповіді, тому ви не можете змінити його.';
$_lang['ticket_comment_err_parent'] = 'Коментар, на який ви відповідаєте не існує.';
$_lang['ticket_comment_err_comment'] = 'Цей коментар не існує.';
$_lang['ticket_comment_err_vote_own'] = 'Ви не можете голосувати за свій власний коментар.';
$_lang['ticket_comment_err_vote_already'] = 'Ви вже голосували за цей коментар.';
$_lang['ticket_comment_err_wrong_guest_ip'] = 'Ви не авторизовані і ваш ip не збігається з ip автора цього коментаря.';
$_lang['ticket_comment_err_empty'] = 'Ви забули написати коментар.';
$_lang['ticket_comment_err_email'] = 'Ви вказали невірний email.';
$_lang['ticket_comment_err_guest_edit'] = 'Вам не дозволено редагувати коментарі.';
$_lang['ticket_comment_err_captcha'] = 'Вказаний невірний код захисту від спаму.';
$_lang['ticket_comment_err_no_email'] = 'Вам потрібно вказати email в налаштуваннях вашого облікового запису.';

$_lang['ticket_publishedon'] = 'Опубліковано';
$_lang['ticket_pagetitle'] = 'Заголовок';
$_lang['ticket_author'] = 'Автор';
$_lang['ticket_delete'] = 'Видалити тікет';
$_lang['ticket_delete_text'] = 'Ви певні, що хочете видалити цей тікет?';
$_lang['ticket_create'] = 'Створити тікет';
$_lang['ticket_disable_jevix'] = 'Відключити Jevix';
$_lang['ticket_disable_jevix_help'] = 'Виводити контент сторінки без фільтрації сніпетом Jevix. <b>Дуже небезпечно</b>, так як будь-який користувач, що створює сторінку, зможе атакувати ваш сайт (XSS, LFI тощо)';
$_lang['ticket_process_tags'] = 'Виконувати теги MODX';
$_lang['ticket_process_tags_help'] = 'Типово, теги в квадратних дужках виводяться як є, без обробки парсером. Якщо включите, то на цій сторінці будуть запускатись сніпети, чанки тощо.';
$_lang['ticket_private'] = 'Закрытый тікет';
$_lang['ticket_private_help'] = 'Якщо включене, то користувачу необхідний дозвіл "ticket_view_private" для перегляду цього тікета.';
$_lang['ticket_show_in_tree'] = 'Показывать в дереве';
$_lang['ticket_show_in_tree_help'] = 'По умолчанию, тикеты не показываются в дереве ресурсов MODX, чтобы не нагружать его.';
$_lang['ticket_pagetitle'] = 'Заголовок';
$_lang['ticket_content'] = 'Зміст';
$_lang['ticket_publish'] = 'Публікувати';
$_lang['ticket_preview'] = 'Перегляд';
$_lang['ticket_save'] = 'Відправити';
$_lang['ticket_read_more'] = 'Читати далі';

$_lang['ticket_thread'] = 'Гілка коментарів';
$_lang['ticket_thread_name'] = 'Ім`я гілки';
$_lang['ticket_thread_intro'] = 'Комментарі, що сгруповані за гілками. Звичайно, одна гілка - це коментарі однієї сторінки.';
$_lang['ticket_thread_createdon'] = 'Створена';
$_lang['ticket_thread_editedon'] = 'Змінена';
$_lang['ticket_thread_deletedon'] = 'Видалена';
$_lang['ticket_thread_comments'] = 'Коментарі';
$_lang['ticket_thread_resource'] = 'Id тікета';
$_lang['ticket_thread_delete'] = 'Вимикнути гілку';
$_lang['ticket_thread_undelete'] = 'Вмикнути гілку';
$_lang['ticket_thread_close'] = 'Закрити гілку';
$_lang['ticket_thread_open'] = 'Відкрити гілку';
$_lang['ticket_thread_remove'] = 'Видалити разом з коментарями';
$_lang['ticket_thread_remove_confirm'] = 'Ви дійсно хочете видалити <b>всю</b> гілку коментарів? Ця операція незворотня!';
$_lang['ticket_thread_view'] = 'Проглянути на сайті';
$_lang['ticket_thread_err_deleted'] = 'Коментування відключене.';
$_lang['ticket_thread_err_closed'] = 'Додавання нових коментарів заборонене.';
$_lang['ticket_thread_manage_comments'] = 'Управління коментарями';

$_lang['ticket_date_now'] = 'Тільки що';
$_lang['ticket_date_today'] = 'Сьогодні о';
$_lang['ticket_date_yesterday'] = 'Вчора о';
$_lang['ticket_date_tomorrow'] = 'Завтра о';
$_lang['ticket_date_minutes_back'] = '["[[+minutes]] хвилину тому","[[+minutes]] хвилини тому","[[+minutes]] хвилин тому"]';
$_lang['ticket_date_minutes_back_less'] = 'менше хвилини тому';
$_lang['ticket_date_hours_back'] = '["[[+hours]] година тому","[[+hours]] години тому","[[+hours]] годин тому"]';
$_lang['ticket_date_hours_back_less'] = 'менше години тому';
$_lang['ticket_date_months'] = '["січень","лютий","березень","квітень","травень","червень","липеь","серпень","вересень","жовтень","листопад","грудень"]';

$_lang['ticket_like'] = 'Подобається';
$_lang['ticket_dislike'] = 'Не подобається';
$_lang['ticket_refrain'] = 'Переглянути рейтинг';
$_lang['ticket_rating_total'] = 'Всього';
$_lang['ticket_rating_and'] = 'і';