Ext.provide('Phlexible.mediamanager.UploadChecker');

Ext.require('Phlexible.mediamanager.FileReplaceWindow');
Ext.require('Phlexible.mediamanager.FileUploadWizard');
Ext.require('Phlexible.mediamanager.model.TempFile');

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
    /**
     * @property {Phlexible.mediamanager.model.TempFile} currentFile
     */

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

    getCurrentFile: function() {
        return this.currentFile;
    },

    discardAllFiles: function(values, callback, scope) {
        values.all = true;
        this.send('discard', values, callback, scope)
    },

    discardFile: function(values, callback, scope) {
        this.send('discard', values, callback, scope)
    },

    keepFile: function(values, callback, scope) {
        this.send('keep', values, callback, scope)
    },

    replaceFile: function(values, callback, scope) {
        this.send('replace', values, callback, scope)
    },

    saveFileVersion: function(values, callback, scope) {
        this.send('save_version', values, callback, scope)
    },

    saveFile: function(values, callback, scope) {
        this.send('save', values, callback, scope)
    },

    send: function(action, values, callback, scope) {
        Ext.apply(values, {
            action: action,
            temp_key: this.currentFile.get('temp_key'),
            temp_id: this.currentFile.get('temp_id')
        });

        Ext.Ajax.request({
            url: Phlexible.Router.generate('mediamanager_upload_save'),
            params: values,
            success: function (response) {
                var data = Ext.decode(response.responseText);

                if (data.success) {
                    this.fireEvent('reload');
                }

                if (callback) {
                    if (!scope) {
                        scope = this;
                    }
                    callback.call(scope);
                }
            },
            scope: this
        });
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

        this.currentFile = new Phlexible.mediamanager.model.TempFile(data);

        if (data.wizard) {
            if (this.replace) {
                this.replace.hide();
            }
            if (!this.wizard) {
                this.wizard = new Phlexible.mediamanager.FileUploadWizard({
                    uploadChecker: this
                });
            }
            this.wizard.removeLoadmask();
            this.wizard.show();
            this.wizard.loadFile();
        }
        else {
            if (this.wizard) {
                this.wizard.hide();
            }
            if (!this.replace) {
                this.replace = new Phlexible.mediamanager.FileReplaceWindow({
                    uploadChecker: this
                });
            }
            this.replace.show();
            this.replace.loadFile();
        }

        this.running = true;
    }
});
