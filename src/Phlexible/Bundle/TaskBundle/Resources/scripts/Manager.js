Phlexible.tasks.Manager = {
    setStatus: function(task_id, new_status, comment, callback, scope) {
        Ext.Ajax.request({
            url: '/bla',//Phlexible.Router.generate('tasks_create_status'),
            params: {
                task_id: task_id,
                new_status: new_status,
                comment: encodeURIComponent(comment)
            },
            success: callback || Ext.emptyFn,
            scope: scope || this
        });
    }
};
