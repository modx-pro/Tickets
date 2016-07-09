Ext.Loader.load([
    MODx.config.assets_url + 'components/tickets/js/mgr/misc/strftime-min-1.3.js'
]);

Tickets.utils.formatDate = function (string) {
    if (string && string != '0000-00-00 00:00:00' && string != '-1-11-30 00:00:00' && string != 0) {
        var date = /^[0-9]+$/.test(string)
            ? new Date(string * 1000)
            : new Date(string.replace(/(\d+)-(\d+)-(\d+)/, '$2/$3/$1'));

        return date.strftime(MODx.config['tickets.date_format']);
    }
    else {
        return '&nbsp;';
    }
};

Tickets.utils.userLink = function (value, id) {
    if (!value) {
        return '';
    }
    else if (!id) {
        return value;
    }
    var action = MODx.action
        ? MODx.action['security/user/update']
        : 'security/user/update';
    var url = 'index.php?a=' + action + '&id=' + id;

    return '<a href="' + url + '" target="_blank" class="tickets-link">' + value + '</a>'
};

Tickets.utils.ticketLink = function (value, id, blank) {
    if (!value) {
        return '';
    }
    else if (!id) {
        return value;
    }
    var action = MODx.action
        ? MODx.action['resource/update']
        : 'resource/update';
    var url = 'index.php?a=' + action + '&id=' + id;

    return blank
        ? '<a href="' + url + '" class="tickets-link" target="_blank">' + value + '</a>'
        : '<a href="' + url + '" class="tickets-link">' + value + '</a>';
};

Tickets.utils.getMenu = function (actions, grid, selected) {
    var menu = [];
    var cls, icon, title, action;

    var has_delete = false;
    for (var i in actions) {
        if (!actions.hasOwnProperty(i)) {
            continue;
        }

        var a = actions[i];
        if (!a['menu']) {
            if (a == '-') {
                menu.push('-');
            }
            continue;
        }
        else if (menu.length > 0 && !has_delete && (/^remove/i.test(a['action']) || /^delete/i.test(a['action']))) {
            menu.push('-');
            has_delete = true;
        }

        if (selected.length > 1) {
            if (!a['multiple']) {
                continue;
            }
            else if (typeof(a['multiple']) == 'string') {
                a['title'] = a['multiple'];
            }
        }

        cls = a['cls'] ? a['cls'] : '';
        icon = a['icon'] ? a['icon'] : '';
        title = a['title'] ? a['title'] : a['title'];
        action = a['action'] ? grid[a['action']] : '';

        menu.push({
            handler: action,
            text: String.format(
                '<span class="{0}"><i class="x-menu-item-icon {1}"></i>{2}</span>',
                cls, icon, title
            ),
        });
    }

    return menu;
};


Tickets.utils.renderActions = function (value, props, row) {
    var res = [];
    var cls, icon, title, action, item;
    for (var i in row.data.actions) {
        if (!row.data.actions.hasOwnProperty(i)) {
            continue;
        }
        var a = row.data.actions[i];
        if (!a['button']) {
            continue;
        }

        cls = a['cls'] ? a['cls'] : '';
        icon = a['icon'] ? a['icon'] : '';
        action = a['action'] ? a['action'] : '';
        title = a['title'] ? a['title'] : '';

        //noinspection HtmlUnknownAttribute
        item = String.format(
            '<li class="{0}"><button class="btn btn-default {1}" action="{2}" title="{3}"></button></li>',
            cls, icon, action, title
        );

        res.push(item);
    }

    return String.format(
        '<ul class="tickets-row-actions">{0}</ul>',
        res.join('')
    );
};