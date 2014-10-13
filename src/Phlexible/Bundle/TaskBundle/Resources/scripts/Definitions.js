Ext.namespace('Phlexible.tasks.menuhandle', 'Phlexible.tasks.model');

Phlexible.tasks.ViewTemplate = new Ext.XTemplate(
    '<div class="p-tasks-view">',
        '<table cellpadding="0" cellspacing="5">',
            '<colgroup>',
                '<col width="100" />',
                '<col width="240" />',
            '</colgroup>',
            '<tr>',
                '<th>{[Phlexible.tasks.Strings.task]}</th>',
                '<td>{title}</td>',
            '</tr>',
            '<tr>',
                '<th>{[Phlexible.tasks.Strings.task]}</th>',
                '<td>{text}</td>',
            '</tr>',
            '<tr>',
                '<th>{[Phlexible.tasks.Strings.status]}</th>',
                '<td>{[Phlexible.inlineIcon(\"p-task-status_\"+values.status+\"-icon\")]} {[Phlexible.tasks.Strings.get(values.status)]}</td>',
            '</tr>',
            '<tr>',
                '<th colspan="2">{[Phlexible.tasks.Strings.description]}</th>',
            '</tr>',
            '<tr>',
                '<td colspan="2">{description}</td>',
            '</tr>',
            '<tr>',
                '<th>{[Phlexible.tasks.Strings.assigned_to]}</th>',
                '<td>{assigned_user}</td>',
            '</tr>',
            '<tr>',
                '<th>{[Phlexible.tasks.Strings.create_user]}</th>',
                '<td>{create_user}</td>',
            '</tr>',
            '<tr>',
                '<th>{[Phlexible.tasks.Strings.create_date]}</th>',
                '<td>{create_date}</td>',
            '</tr>',
        '</table>',
    '</div>'
);

Phlexible.tasks.CommentsTemplate = new Ext.XTemplate(
    '<div class="p-tasks-comments">',
        '<tpl for=".">',
        '<div class="p-tasks-comment">',
            '<div class="p-tasks-by">{create_user} kommentierte - {create_date}</div>',
            '<div class="p-tasks-text">{comment}</div>',
        '</div>',
        '</tpl>',
    '</div>'
);

Phlexible.tasks.TransitionsTemplate = new Ext.XTemplate(
    '<div class="p-tasks-transitions">',
        '<tpl for=".">',
        '<div class="p-tasks-transition">',
            '<div class="p-tasks-by">{create_user} änderte - {create_date}</div>',
            '<div class="p-tasks-text">' +
                '<div style="float: left;">{[Phlexible.inlineIcon(\"p-task-status_\" + values.old_state + \"-icon\")]} {old_state}</div>' +
                '<div style="margin-left: 120px;">{[Phlexible.inlineIcon(\"p-task-goto-icon\")]} ' +
                '{[Phlexible.inlineIcon(\"p-task-status_\" + values.new_state + \"-icon\")]} {new_state}</div>' +
                '<div style="clear: left; "></div>' +
            '</div>',
        '</div>',
        '</tpl>',
    '</div>'
);