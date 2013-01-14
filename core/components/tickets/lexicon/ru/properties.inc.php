<?php
/**
 * Properties Russian Lexicon Entries for Tickets
 *
 * @package tickets
 * @subpackage lexicon
 */
$_lang['tickets.action'] = 'Режим работы сниппета';
$_lang['tickets.limit'] = 'Лимит выборки результатов';
$_lang['tickets.start'] = 'Начало выборки';
$_lang['tickets.sortBy'] = 'Сортировка выборки';
$_lang['tickets.sortDir'] = 'Направление сортировки';
$_lang['tickets.tpl'] = 'Чанк оформления для каждого результата';
$_lang['tickets.tplFormCreate'] = 'Чанк для создания нового тикета';
$_lang['tickets.tplFormUpdate'] = 'Чанк для обновления существующего тикета';
$_lang['tickets.tplSectionRow'] = 'Чанк для оформления раздела вопросов в форме';
$_lang['tickets.tplPreview'] = 'Чанк для предпросмотра тикета перед публикацией';
$_lang['tickets.cacheKey'] = 'Имя кэша сниппета. Если пустое - кэширование результатов будет отключено.';
$_lang['tickets.toPlaceholder'] = 'Если не пусто, сниппет сохранит все данные в плейсхолдер с этим именем, вместо вывода не экран.';

$_lang['tickets.thread'] = 'Имя ветки комментариев. По умолчанию, "resource-[[*id]]".';
$_lang['tickets.fastMode'] = 'Если включено - чанк комментария не будет полностью обрабатываться, скрипт только заменит плейсхолдеры на значения. Все фильтры, чанки, сниппеты и прочее будет вырезано.';
$_lang['tickets.dateFormat'] = 'Формат даты комментария, для функции date()';
$_lang['tickets.gravatarIcon'] = 'Если аватарка пользователя не найдена, грузить эту картинку на замену.';
$_lang['tickets.gravatarSize'] = 'Размер загружаемого аватара';
$_lang['tickets.gravatarUrl'] = 'Адрес для загрузки аватаров';
$_lang['tickets.tplCommentForm'] = 'Чанк для формы добавления нового комментария';
$_lang['tickets.tplComment'] = 'Чанк оформления одного комментария';
$_lang['tickets.tplComments'] = 'Обертка для всех комментариев страницы';
$_lang['tickets.tplLoginToComment'] = 'Чанк для показа неавторизованным пользователям';
$_lang['tickets.tplCommentEmailOwner'] = 'Чанк для уведомления владельца тикета о новом комментарии.';
$_lang['tickets.tplCommentEmailReply'] = 'Чанк для уведомления пользователя о том, что на его комментарий появился ответ.';