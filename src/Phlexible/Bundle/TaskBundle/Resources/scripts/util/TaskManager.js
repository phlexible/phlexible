Ext.provide('Phlexible.tasks.util.TaskManager');

Phlexible.tasks.util.TaskManager = {
    comment: function (task_id, comment, callback, scope) {
        Ext.Ajax.request({
            url: Phlexible.Router.generate('tasks_create_comment'),
            params: {
                id: task_id,
                comment: encodeURIComponent(comment)
            },
            callback: function(options, success, response) {
                if (callback) {
                    var result = Ext.decode(response.responseText);
                    callback(success, result, options);
                }
            },
            scope: scope || this
        });
    },

    assignToMe: function (task_id, callback, scope) {
        Ext.Ajax.request({
            url: Phlexible.Router.generate('tasks_assign'),
            params: {
                id: task_id,
                recipient: Phlexible.Config.get('user.id'),
                comment: encodeURIComponent(comment)
            },
            callback: function(options, success, response) {
                if (callback) {
                    var result = Ext.decode(response.responseText);
                    callback(success, result, options);
                }
            },
            scope: scope || this
        });
    },

    assign: function (task_id, recipient, comment, callback, scope) {
        Ext.Ajax.request({
            url: Phlexible.Router.generate('tasks_assign'),
            params: {
                id: task_id,
                recipient: recipient,
                comment: encodeURIComponent(comment)
            },
            callback: function(options, success, response) {
                if (callback) {
                    var result = Ext.decode(response.responseText);
                    callback(success, result, options);
                }
            },
            scope: scope || this
        });
    },

    setStatus: function (task_id, new_status, comment, callback, scope) {
        Ext.Ajax.request({
            url: Phlexible.Router.generate('tasks_create_transition'),
            params: {
                id: task_id,
                new_status: new_status,
                comment: encodeURIComponent(comment)
            },
            callback: function(options, success, response) {
                if (callback) {
                    var result = Ext.decode(response.responseText);
                    callback(success, result, options);
                }
            },
            scope: scope || this
        });
    }
};
