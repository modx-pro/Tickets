<?php
/*
 * Properties Ukrainian Lexicon Entries
 *
 * */

$_lang['tickets_prop_limit'] = 'Ліміт вибірки результатів.';
$_lang['tickets_prop_offset'] = 'Скільки результатів пропустити від початку вибірки.';
$_lang['tickets_prop_depth'] = 'Глибина пошуку ресурсів від кожного батька.';
$_lang['tickets_prop_sortby'] = 'Поле сортування.';
$_lang['tickets_prop_sortdir'] = 'Напрям сортування.';
$_lang['tickets_prop_parents'] = 'Список батьківських категорій через кому. За замовчуванням обмежено поточним батьком. 0 — без обмеження.';
$_lang['tickets_prop_resources'] = 'Список id ресурсів через кому. Мінус перед id виключає ресурс із вибірки.';
$_lang['tickets_prop_threads'] = 'Список id гілок коментарів через кому. Мінус перед id виключає гілку з вибірки.';
$_lang['tickets_prop_where'] = 'JSON з додатковими умовами вибірки.';
$_lang['tickets_prop_tvPrefix'] = 'Префікс для плейсхолдерів TV, наприклад «tv.». За замовчуванням порожній.';
$_lang['tickets_prop_includeContent'] = 'Обирати поле content ресурсів.';
$_lang['tickets_prop_includeTVs'] = 'Список імен TV через кому. Наприклад: action,time дають плейсхолдери [[+action]] і [[+time]].';
$_lang['tickets_prop_toPlaceholder'] = 'Якщо задано, сніпет зберігає вивід у цей плейсхолдер замість друку на екран.';
$_lang['tickets_prop_outputSeparator'] = 'Рядок між результатами.';

$_lang['tickets_prop_showLog'] = 'Показувати службовий лог сніпета. Лише для користувачів у контексті mgr.';
$_lang['tickets_prop_showUnpublished'] = 'Показувати неопубліковані ресурси.';
$_lang['tickets_prop_showDeleted'] = 'Показувати видалені ресурси.';
$_lang['tickets_prop_showHidden'] = 'Показувати ресурси, приховані в меню.';
$_lang['tickets_prop_fastMode'] = 'Якщо увімкнено, у чанк потрапляють лише значення з БД. Необроблені теги MODX (фільтри, виклики сніпетів) вирізаються.';

$_lang['tickets_prop_action'] = 'Режим роботи сніпета.';
$_lang['tickets_prop_cacheKey'] = 'Ім’я кешу сніпета. Порожнє значення вимикає кеш.';
$_lang['tickets_prop_cacheTime'] = 'Час кешування.';
$_lang['tickets_prop_thread'] = 'Ім’я гілки коментарів. За замовчуванням resource-[[*id]].';
$_lang['tickets_prop_user'] = 'Лише елементи, створені цим користувачем.';

$_lang['tickets_prop_tpl'] = 'Чанк оформлення одного результату.';
$_lang['tickets_prop_tplFormCreate'] = 'Чанк форми створення тікета.';
$_lang['tickets_prop_tplFormUpdate'] = 'Чанк форми оновлення тікета.';
$_lang['tickets_prop_tplSectionRow'] = 'Чанк рядка розділу у формі.';
$_lang['tickets_prop_tplPreview'] = 'Чанк попереднього перегляду тікета.';
$_lang['tickets_prop_tplCommentForm'] = 'Форма додавання коментаря (чанк або HTML).';
$_lang['tickets_prop_tplCommentFormGuest'] = 'Форма коментаря для гостей.';
$_lang['tickets_prop_tplCommentAuth'] = 'Чанк коментаря для авторизованого користувача.';
$_lang['tickets_prop_tplCommentGuest'] = 'Чанк коментаря для гостей.';
$_lang['tickets_prop_tplCommentDeleted'] = 'Чанк видаленого коментаря.';
$_lang['tickets_prop_tplComments'] = 'Обгортка всіх коментарів на сторінці.';
$_lang['tickets_prop_tplLoginToComment'] = 'Чанк із вимогою увійти перед коментуванням.';
$_lang['tickets_prop_tplCommentEmailOwner'] = 'Чанк листа власнику тікета про новий коментар.';
$_lang['tickets_prop_tplCommentEmailReply'] = 'Чанк листа про відповідь на коментар.';
$_lang['tickets_prop_tplCommentEmailSubscription'] = 'Чанк листа підписнику про новий коментар у темі.';
$_lang['tickets_prop_tplCommentEmailBcc'] = 'Чанк BCC-листа адміністраторам про новий коментар.';
$_lang['tickets_prop_tplTicketEmailBcc'] = 'Чанк BCC-листа адміністраторам про новий тікет.';
$_lang['tickets_prop_tplTicketEmailSubscription'] = 'Чанк листа підписнику розділу про новий тікет.';
$_lang['tickets_prop_tplCommentEmailUnpublished'] = 'Чанк сповіщення про неопублікований коментар.';
$_lang['tickets_prop_tplWrapper'] = 'Чанк-обгортка всього виводу ([[+output]]). Не працює з toSeparatePlaceholders.';
$_lang['tickets_prop_threadUrl'] = 'Абсолютний URL для переходу до коментаря з адмінки. Для коментування об’єктів, що не є ресурсами.';

$_lang['tickets_prop_commentsDepth'] = 'Максимальна глибина гілки коментарів.';
$_lang['tickets_prop_autoPublish'] = 'Публікувати коментарі авторизованих користувачів без премодерації.';
$_lang['tickets_prop_autoPublishGuest'] = 'Публікувати коментарі гостей без премодерації.';
$_lang['tickets_prop_formBefore'] = 'Розмістити форму коментування перед списком коментарів.';
//$_lang['tickets_prop_dateFormat'] = 'Формат дати коментаря для date().';
$_lang['tickets_prop_gravatarIcon'] = 'Іконка Gravatar за замовчуванням, якщо аватара немає.';
$_lang['tickets_prop_gravatarSize'] = 'Розмір аватара в пікселях.';
$_lang['tickets_prop_gravatarUrl'] = 'URL сервісу Gravatar.';

$_lang['tickets_prop_allowedFields'] = 'Поля тікета, які можна заповнювати. Дозволені імена TV.';
$_lang['tickets_prop_requiredFields'] = 'Обов’язкові поля форми.';
$_lang['tickets_prop_bypassFields'] = 'Поля тікета, які не фільтруються під час збереження.';
$_lang['tickets_prop_redirectUnpublished'] = 'Документ, куди направити користувача після створення неопублікованого тікета.';
$_lang['tickets_prop_sections_parents'] = 'За замовчуванням показуються всі розділи. Обмежте списком id батьків через кому.';
$_lang['tickets_prop_sections_resources'] = 'За замовчуванням показуються всі розділи. Обмежте списком id розділів через кому.';
$_lang['tickets_prop_sections_sortby'] = 'Поле сортування списку розділів.';
$_lang['tickets_prop_sections_sortdir'] = 'Напрям сортування списку розділів.';
$_lang['tickets_prop_sections_context'] = 'Контексти для пошуку розділів через кому.';

$_lang['tickets_prop_meta_tpl'] = 'Чанк інформації про тікет.';
$_lang['tickets_prop_getSection'] = 'Додатковий запит до БД за батьківським розділом?';
$_lang['tickets_prop_getUser'] = 'Додатковий запит до БД за профілем автора?';

$_lang['tickets_prop_allowGuest'] = 'Дозволити коментування гостям?';
$_lang['tickets_prop_allowGuestEdit'] = 'Дозволити гостям редагувати свої коментарі?';
$_lang['tickets_prop_allowGuestEmails'] = 'Надсилати гостям листи про відповіді?';
$_lang['tickets_prop_enableCaptcha'] = 'Увімкнути захист від спаму для гостей?';
$_lang['tickets_prop_minCaptcha'] = 'Мінімальне число для генерації captcha.';
$_lang['tickets_prop_maxCaptcha'] = 'Максимальне число для генерації captcha.';

$_lang['tickets_prop_allowFiles'] = 'Дозволити завантаження файлів на сервер.';
$_lang['tickets_prop_source'] = 'Id медіаджерела для завантажень. За замовчуванням — tickets.source_default.';
$_lang['tickets_prop_tplFiles'] = 'Контейнер завантажувача й списку вже завантажених файлів.';
$_lang['tickets_prop_tplFile'] = 'Чанк завантаженого файлу (не зображення).';
$_lang['tickets_prop_tplImage'] = 'Чанк завантаженого зображення.';

$_lang['tickets_prop_getFiles'] = 'Показувати список завантажених файлів?';
$_lang['tickets_prop_unusedFiles'] = 'Показувати лише файли без посилань у контенті тікета.';
$_lang['tickets_prop_meta_tplFile'] = 'Чанк файлу в списку.';

$_lang['tickets_prop_class'] = 'Клас для вибірки результатів.';
$_lang['tickets_prop_tree'] = 'Показувати коментарі деревом. Ні: плоский список (пагінація limit/offset).';
$_lang['tickets_prop_separatePlaceholder'] = 'Якщо так, кожен коментар іде в окремий плейсхолдер замість спільного виводу.';
$_lang['tickets_prop_TicketsInit'] = 'Встановіть 1, щоб підключити форму підписки й ініціалізувати frontend-скрипти Tickets на довільних сторінках.';
$_lang['tickets_prop_createdby'] = 'ID автора (user), на якого оформлюється підписка через форму.';
