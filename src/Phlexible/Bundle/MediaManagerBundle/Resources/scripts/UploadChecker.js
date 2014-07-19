Phlexible.mediamanager.UploadChecker = function() {
    this.running = false;
};
Ext.extend(Phlexible.mediamanager.UploadChecker, Ext.util.Observable, {
    check: function() {
        this.next();
    },

    next: function() {
        if (this.isRunning()) {
            return;
        }

        Ext.Ajax.request({
            url: Phlexible.Router.generate('mediamanager_upload_check'),
            success: this.onCheckResponse,
            scope: this
        });
    },

    isRunning: function() {
        return this.running;
    },

    getCurrent: function() {
        return this.current;
    },

    onCheckResponse: function(response) {
        if (!response.responseText) {
            // this.getFilesGrid().getStore().reload();
            return;
        }

        var data = Ext.decode(response.responseText);

        if (!data) {
            // this.getFilesGrid().getStore().reload();
            return;
        }

        this.current = data;

        if (data.wizard) {
            if (this.replace) {
                this.replace.hide();
            }
            if (!this.wizard) {
                this.wizard = new Phlexible.mediamanager.FileUploadWizard({
                    uploadChecker: this,
                    listeners: {
                        update: function () {
                            this.getFilesGrid().getStore().reload();
                        },
                        scope: this
                    }
                });
            }
            this.wizard.show();
            this.wizard.loadFile();
        }
        else {
            if (this.wizard) {
                this.wizard.hide();
            }
            if (!this.replace) {
                this.replace = new Phlexible.mediamanager.FileReplaceWindow({
                    uploadChecker: this,
                    listeners: {
                        update: function () {
                            this.getFilesGrid().getStore().reload()
                        },
                        scope: this
                    }
                });
            }
            this.replace.show();
            this.replace.loadFile();
        }

        this.running = true;

        return;

        var store = this.getFilesGrid().getStore();
        if (Phlexible.Config.get('mediamanager.upload.enable_upload_sort')) {
            if (!store.lastOptions) store.lastOptions = {};
            if (!store.lastOptions.params) store.lastOptions.params = {};
            store.lastOptions.params.start = 0;
            var sort = store.getSortState();
            if (sort.field != 'create_time' || sort.direction != 'DESC') {
                store.sort('create_time', 'DESC');
            }
            else {
                store.reload();
            }
        }
        else {
            store.reload();
        }
    }
});
