Phlexible.elements.Clipboard = function () {
    this.text = null;
    this.item = null;
    this.type = null;
    this.active = false;

    Phlexible.gui.util.Frame.prototype.removeSplash = Phlexible.gui.util.Frame.prototype.removeSplash.createSequence(function () {
        this.clipBtn = Phlexible.Frame.taskbar.trayPanel.add({
            id: 'clipboard',
            cls: 'x-btn-icon',
            iconCls: 'p-element-clipboard_inactive-icon',
            handler: this.copy,
            scope: this
        });
    }, this);

    this.addEvents({
        "set": true,
        "clear": true
    });
};

Ext.extend(Phlexible.elements.Clipboard, Ext.util.Observable, {
    strings: Phlexible.elements.Strings,

    set: function (text, item, type) {
        if (!type) type = null;

        this.setText(text);
        this.setItem(item);
        this.setType(type);

        this.setActive();

        if (type) {
            Phlexible.msg(this.strings.clipboard, String.format(this.strings.copy_text_type, text, type));
        } else {
            Phlexible.msg(this.strings.clipboard, String.format(this.strings.copy_text, text));
        }

        this.fireEvent('set', this);
    },

    setItem: function (item) {
        this.item = item;
    },

    getItem: function () {
        return this.item;
    },

    setText: function (newText) {
        this.text = newText;
    },

    getText: function () {
        return this.text;
    },

    setType: function (newType) {
        this.type = newType;
    },

    getType: function () {
        return this.type;
    },

    setActive: function () {
        this.active = true;

        this.clipBtn.setIconClass('p-element-clipboard_active-icon');
        this.clipBtn.show();
    },

    isActive: function () {
        return this.active ? true : false;
    },

    setInactive: function () {
        this.active = false;

        this.clipBtn.setIconClass('p-element-clipboard_inactive-icon');
        this.clipBtn.hide();
    },

    isInactive: function () {
        return this.active ? false : true;
    },

    clear: function () {
        this.item = null;
        this.text = null;
        this.setInactive();

        this.fireEvent('clear', this);
    }
});

Ext.onReady(function () {
    Phlexible.Clipboard = new Phlexible.elements.Clipboard();
});

