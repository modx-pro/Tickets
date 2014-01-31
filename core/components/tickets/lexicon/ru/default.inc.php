<?php
/**
 * Default Russian Lexicon Entries for Tickets
 */

include_once('setting.inc.php');

$_lang['tickets'] = 'Тикеты';
$_lang['comments'] = 'Комментарии';
$_lang['threads'] = 'Ветви комментариев';
$_lang['tickets_section'] = 'Раздел тикетов';
$_lang['ticket'] = 'Тикет';
$_lang['ticket_all'] = 'Все';
$_lang['ticket_menu_desc'] = 'Управление комментариями и не только.';
$_lang['comments_all'] = 'Все комментарии';

$_lang['tickets_section_create_here'] = 'Раздел с тикетами';
$_lang['tickets_section_new'] = 'Новый раздел тикетов';
$_lang['tickets_section_management'] = 'Управление тикетами';
$_lang['tickets_section_duplicate'] = 'Копировать секцию';
$_lang['tickets_section_unpublish'] = 'Снять с публикации';
$_lang['tickets_section_publish'] = 'Опубликовать секцию';
$_lang['tickets_section_undelete'] = 'Восстановить секцию';
$_lang['tickets_section_delete'] = 'Удалить секцию';
$_lang['tickets_section_view'] = 'Просмотреть на сайте';

$_lang['ticket_create_here'] = 'Создать тикет';

$_lang['ticket_no_comments'] = 'На этой странице еще нет комментариев. Вы можете написать первый.';
$_lang['err_no_jevix'] = 'Для работы необходим сниппет Jevix. Вы должны установить его из репозитория MODX.';
$_lang['tickets_err_unknown'] = 'Произошла неизвестная ошибка.';
$_lang['tickets_message_close_all'] = 'закрыть все';
$_lang['ticket_err_id'] = 'Тикет с указанным id = [[+id]] не найден.';
$_lang['ticket_err_wrong_user'] = 'Вы пытаетесь обновить тикет, который вам не принадлежит.';
$_lang['ticket_err_no_auth'] = 'Вы должны авторизоваться, чтобы создать тикет.';
$_lang['ticket_err_wrong_parent'] = 'Указан неверный раздел для тикета.';
$_lang['ticket_err_wrong_resource'] = 'Указан неверный тикет.';
$_lang['ticket_err_wrong_thread'] = 'Указана неверная ветвь комментариев.';
$_lang['ticket_err_access_denied'] = 'Доступ запрещен.';
$_lang['ticket_err_form'] = 'В форме содержатся ошибки. Пожалуйста, исправьте их.';
$_lang['ticket_err_empty_comment'] = 'Комментарий не может быть пустым.';
$_lang['ticket_err_deleted_comment'] = 'Вы пытаетесь отредактировать удалённый комментарий.';
$_lang['ticket_err_unpublished_comment'] = 'Этот комментарий еще не был опубликован.';
$_lang['ticket_err_ticket'] = 'Указанный тикет не существует.';
$_lang['ticket_err_vote_own'] = 'Вы не можете голосовать за свой тикет.';
$_lang['ticket_err_vote_already'] = 'Вы уже голосовали за этот тикет.';
$_lang['ticket_unpublished_comment'] = 'Ваш комментарий будет опубликован после проверки.';
$_lang['permission_denied'] = 'У вас недостаточно прав для этого действия.';
$_lang['field_required'] = 'Это поле обязательно.';
$_lang['ticket_clear'] = 'Очистить';

$_lang['ticket_comment_intro'] = '';
$_lang['ticket_comment_all_intro'] = 'Здесь собраны комментарии со всего сайта.';
$_lang['ticket_comment_deleted_text'] = 'Комментарий был удален.';
$_lang['ticket_comment_update'] = 'Изменить комментарий';
$_lang['ticket_comment_remove'] = 'Уничтожить вместе с потомками';
$_lang['ticket_comment_remove_confirm'] = 'Вы уверены, что хотите окончательно удалить <b>ветвь комментариев</b>, начиная с этого? Эта операция необратима!';
$_lang['ticket_comment_delete'] = 'Удалить комментарий';
$_lang['ticket_comment_undelete'] = 'Восстановить комментарий';
$_lang['ticket_comment_publish'] = 'Опубликовать комментарий';
$_lang['ticket_comment_unpublish'] = 'Снять с публикации';
$_lang['ticket_comment_viewauthor'] = 'Открыть страницу автора';

$_lang['ticket_comment_name'] = 'Автор';
$_lang['ticket_comment_text'] = 'Комментарий';
$_lang['ticket_comment_createdon'] = 'Написан';
$_lang['ticket_comment_editedon'] = 'Изменен';
$_lang['ticket_comment_deletedon'] = 'Удалён';
$_lang['ticket_comment_parent'] = 'Родитель';
$_lang['ticket_comment_email'] = 'Email';
$_lang['ticket_comment_view'] = 'Открыть комментарий на сайте';
$_lang['ticket_comment_reply'] = 'ответить';
$_lang['ticket_comment_edit'] = 'изменить';
$_lang['ticket_comment_create'] = 'Написать комментарий';
$_lang['ticket_comment_preview'] = 'Предпросмотр';
$_lang['ticket_comment_save'] = 'Написать';
$_lang['ticket_comment_was_edited'] = 'Комментарий был изменён';
$_lang['ticket_comment_err_no_auth'] = 'Вы должны авторизоваться, чтобы оставлять комментарии.';
$_lang['ticket_comment_err_wrong_user'] = 'Вы пытаетесь обновить комментарий, который вам не принадлежит.';
$_lang['ticket_comment_err_no_time'] = 'Время для редактирования истекло.';
$_lang['ticket_comment_err_has_replies'] = 'У этого комментария уже есть ответы, поэтому, вы не можете его менять.';
$_lang['ticket_comment_err_parent'] = 'Комментарий, на который вы отвечаете не существует.';
$_lang['ticket_comment_err_comment'] = 'Указанный комментарий не существует.';
$_lang['ticket_comment_err_vote_own'] = 'Вы не можете голосовать за свой комментарий.';
$_lang['ticket_comment_err_vote_already'] = 'Вы уже голосовали за этот комментарий.';

$_lang['ticket_publishedon'] = 'Опубликован';
$_lang['ticket_pagetitle'] = 'Заголовок';
$_lang['ticket_author'] = 'Автор';
$_lang['ticket_delete'] = 'Удалить тикет';
$_lang['ticket_delete_text'] = 'Вы уверены, что хотите удалить этот тикет?';
$_lang['ticket_create'] = 'Создать тикет';
$_lang['ticket_disable_jevix'] = 'Отключить Jevix';
$_lang['ticket_disable_jevix_help'] = 'Выводить контент страницы без фильтрации сниппетом Jevix. <b>Очень опасно</b>, так как любой пользователь, создающий страницу, сможет атаковать ваш сайт (XSS, LFI и т.п.)';
$_lang['ticket_process_tags'] = 'Выполнять теги MODX';
$_lang['ticket_process_tags_help'] = 'По умолчанию, теги в квадратных скобках выводятся как есть, без обработки парсером. Если включите, на этой странице будут запускаться сниппеты, чанки и т.д.';
$_lang['ticket_private'] = 'Закрытый тикет';
$_lang['ticket_private_help'] = 'Если включено, пользователю требуется разрешение "ticket_view_private" для просмотра этого тикета.';
$_lang['ticket_show_in_tree'] = 'Показывать в дереве';
$_lang['ticket_show_in_tree_help'] = 'По умолчанию, тикеты не показываются в дереве ресурсов MODX, чтобы не нагружать его.';
$_lang['ticket_pagetitle'] = 'Заголовок';
$_lang['ticket_content'] = 'Опишите вашу проблему';
$_lang['ticket_publish'] = 'Публиковать';
$_lang['ticket_preview'] = 'Предпросмотр';
$_lang['ticket_save'] = 'Отправить';
$_lang['ticket_read_more'] = 'Читать дальше';

$_lang['ticket_thread'] = 'Ветка комментариев';
$_lang['ticket_thread_name'] = 'Имя ветки';
$_lang['ticket_thread_intro'] = 'Комментарии, сгруппированные по веткам. Обычно, одна ветвь - это комментарии одной страницы.';
$_lang['ticket_thread_createdon'] = 'Создана';
$_lang['ticket_thread_editedon'] = 'Изменена';
$_lang['ticket_thread_deletedon'] = 'Удалёна';
$_lang['ticket_thread_comments'] = 'Комментарии';
$_lang['ticket_thread_resource'] = 'Id тикета';
$_lang['ticket_thread_delete'] = 'Отключить ветку';
$_lang['ticket_thread_undelete'] = 'Включить ветку';
$_lang['ticket_thread_close'] = 'Закрыть ветку';
$_lang['ticket_thread_open'] = 'Открыть ветку';
$_lang['ticket_thread_remove'] = 'Удалить с комментариями';
$_lang['ticket_thread_remove_confirm'] = 'Вы дейcтвительно хотите удалить <b>всю</b> ветвь комментариев? Эта операция необратима!';
$_lang['ticket_thread_view'] = 'Просмотреть на сайте';
$_lang['ticket_thread_err_deleted'] = 'Комментирование отключено.';
$_lang['ticket_thread_err_closed'] = 'Добавление новых комментариев запрещено.';
$_lang['ticket_thread_manage_comments'] = 'Управление комментариями';
$_lang['ticket_thread_subscribed'] = 'Вы подписались на уведомления о новых комментариях в этой теме.';
$_lang['ticket_thread_unsubscribed'] = 'Вы больше не будете получать уведомления о комментариях из этой темы.';

$_lang['ticket_date_now'] = 'Только что';
$_lang['ticket_date_today'] = 'Сегодня в';
$_lang['ticket_date_yesterday'] = 'Вчера в';
$_lang['ticket_date_tomorrow'] = 'Завтра в';
$_lang['ticket_date_minutes_back'] = '["[[+minutes]] минута назад","[[+minutes]] минуты назад","[[+minutes]] минут назад"]';
$_lang['ticket_date_minutes_back_less'] = 'меньше минуты назад';
$_lang['ticket_date_hours_back'] = '["[[+hours]] час назад","[[+hours]] часа назад","[[+hours]] часов назад"]';
$_lang['ticket_date_hours_back_less'] = 'меньше часа назад';
$_lang['ticket_date_months'] = '["января","февраля","марта","апреля","мая","июня","июля","августа","сентября","октября","ноября","декабря"]';

$_lang['ticket_comment_email_owner'] = 'Новый комментарий к вашему тикету "[[+pagetitle]]"';
$_lang['ticket_comment_email_reply'] = 'Ответ на ваш комментарий к тикету "[[+pagetitle]]"';
$_lang['ticket_comment_email_subscription'] = 'Новый комментарий в теме "[[+pagetitle]]"';
$_lang['ticket_comment_email_bcc'] = 'Новый комментарий в теме "[[+pagetitle]]"';
$_lang['ticket_email_bcc'] = 'Новый тикет у вас на сайте - "[[+pagetitle]]"';

$_lang['ticket_like'] = 'Нравится';
$_lang['ticket_dislike'] = 'Не нравится';
$_lang['ticket_refrain'] = 'Посмотреть рейтинг';
$_lang['ticket_rating_total'] = 'Всего';
$_lang['ticket_rating_and'] = 'и';