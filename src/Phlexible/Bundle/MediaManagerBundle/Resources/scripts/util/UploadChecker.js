Ext.provide('Phlexible.mediamanager.UploadChecker');

Ext.require('Phlexible.mediamanager.FileReplaceWindow');

Phlexible.mediamanager.UploadChecker = function(config) {
    config = config || {};

    this.running = false;

    this.addEvents({
        "reload": true
    });

    if (config.listeners) {
        this.listeners = config.listeners;
        Phlexible.mediamanager.UploadChecker.superclass.constructor.call(this);
    }
};
Ext.extend(Phlexible.mediamanager.UploadChecker, Ext.util.Observable, {
    check: function() {
        if (this.isRunning()) {
            return;
        }

        this.next();
    },

    /**
     * @private
     */
    next: function() {
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
            this.running = false;
            if (this.replace) {
                this.replace.hide();
            }
            if (this.wizard) {
                this.wizard.hide();
            }
            return;
        }

        var data = Ext.decode(response.responseText);

        if (!data || !data.temp_id) {
            this.running = false;
            if (this.replace) {
                this.replace.hide();
            }
            if (this.wizard) {
                this.wizard.hide();
            }
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
                        save: function(action, all) {
                            var file = this.getCurrent(),
                                params = {
                                    all: all ? 1 : 0,
                                    temp_key: file.temp_key,
                                    temp_id: file.temp_id,
                                    'do': action
                                };

                            var request = {
                                url: Phlexible.Router.generate('mediamanager_upload_save'),
                                params: params,
                                success: function (response) {
                                    this.fireEvent('reload');
                                    this.next();
                                },
                                failure: function (response) {
                                    var result = Ext.decode(response.responseText);

                                    Ext.MessageBox.alert('Failure', result.msg);
                                },
                                scope: this
                            };

                            Ext.Ajax.request(request);
                        },
                        scope: this
                    }
                });
            }
            this.replace.show();
            this.replace.loadFile();
        }

        this.running = true;
    }
});
