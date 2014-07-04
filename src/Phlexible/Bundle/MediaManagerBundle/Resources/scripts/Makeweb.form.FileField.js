/**
 * @class Ext.form.FileField
 * @extends Ext.form.Field
 * Basic text field.  Can be used as a direct replacement for traditional text inputs, or as the base
 * class for more sophisticated input controls (like {@link Ext.form.TextArea} and {@link Ext.form.ComboBox}).
 * @constructor
 * Creates a new TextField
 * @param {Object} config Configuration options
 */
Ext.form.FileField = Ext.extend(Ext.form.Field, {
    /**
     * @cfg {String} vtypeText A custom error message to display in place of the default message provided
     * for the {@link #vtype} currently set for this field (defaults to '').  Only applies if vtype is set, else ignored.
     */
    /**
     * @cfg {String} vtype A validation type name as defined in {@link Ext.form.VTypes} (defaults to null)
     */
    vtype: null,
    /**
     * @cfg {RegExp} maskRe An input mask regular expression that will be used to filter keystrokes that don't match
     * (defaults to null)
     */
    maskRe: null,
    /**
     * @cfg {Boolean} disableKeyFilter True to disable input keystroke filtering (defaults to false)
     */
    disableKeyFilter: false,
    /**
     * @cfg {Boolean} allowBlank False to validate that the value length > 0 (defaults to true)
     */
    allowBlank: true,
    /**
     * @cfg {String} blankText Error text to display if the allow blank validation fails (defaults to "This field is required")
     */
    blankText: "This field is required",
    /**
     * @cfg {Function} validator A custom validation function to be called during field validation (defaults to null).
     * If available, this function will be called only after the basic validators all return true, and will be passed the
     * current field value and expected to return boolean true if the value is valid or a string error message if invalid.
     */
    validator: null,
    /**
     * @cfg {String} emptyClass The CSS class to apply to an empty field to style the {@link #emptyText} (defaults to
     * 'x-form-empty-field').  This class is automatically added and removed as needed depending on the current field value.
     */
    emptyClass: 'x-form-empty-field',

    /**
     * @cfg {String} emptyImage The image that is displayed when no file is selected (defaults to Ext.BLANK_IMAGE_URL).
     * This image is automatically added and removed as needed depending on the current field value.
     */
    emptyImage: Ext.BLANK_IMAGE_URL,

    addIconCls: 'p-mediamanager-image_add-icon',
    removeIconCls: 'p-mediamanager-image_delete-icon',

    emptyAddText: Phlexible.mediamanager.Strings.click_to_add_file,

    getPlaceholder: function () {
        return Phlexible.component('/phlexiblemediamanager/images/default-img.gif');
    },

    onAdd: function () {

    },

    onClear: function () {
        this.clearFile();
    },

    setFile: function (file_id, file_version, file_name, folder_id) {
        var value = file_id;
        if (file_version) {
            value += ';' + file_version;
        }
        this.setValue(value);
        this.setFileTitle(file_name);
        this.file_id = file_id;
        this.file_version = file_version;
        this.folder_id = folder_id;

        this.fireEvent('select', this, file_id, file_version, file_name, folder_id);
    },

    clearFile: function () {
        this.setValue('');
        this.setFileTitle('');
        this.file_id = null;
        this.file_version = null;
        this.folder_id = null;

        this.removeButton.hide();

        this.fireEvent('clear', this);
    },

    setFileTitle: function (title) {
        this.fileBoxImage.set({
            qtip: title || ''
        });
    },

    // private
    onRender: function (ct, position) {
        Ext.form.Field.superclass.onRender.call(this, ct, position);
        if (!this.el) {
            this.el = ct.createChild({
                    tag: "div",
                    cls: 'x-form-field x-form-empty-field p-form-file p-form-file-image'
                }, position
            );
            this.hiddenEl = this.el.createChild({
                tag: 'input',
                type: 'hidden',
                name: this.name || this.id
            });

            this.leftButtons = this.el.createChild({
                tag: 'div',
                cls: 'FileBoxButtons'
            });

            this.addButton = this.leftButtons.createChild({
                tag: 'img',
                src: Ext.BLANK_IMAGE_URL,
                width: 16,
                height: 16,
                qtip: 'Add'
            });
            this.addButton.addClass(this.addIconCls);
            this.addButton.setVisibilityMode(Ext.Element.DISPLAY);
            if (!this.readOnly) {
                this.addButton.on('click', this.onAdd, this);
            }
            else {
                this.addButton.hide();
            }

            this.removeButton = this.leftButtons.createChild({
                tag: 'img',
                src: Ext.BLANK_IMAGE_URL,
                width: 16,
                height: 16,
                qtip: 'Remove'
            });
            this.removeButton.addClass(this.removeIconCls);
            this.removeButton.setVisibilityMode(Ext.Element.DISPLAY);
            this.removeButton.hide();
            if (!this.readOnly) {
                this.removeButton.on('click', this.onClear, this);
            }

            this.fileBoxImageContainer = this.el.createChild({
                tag: 'div',
                cls: 'FileBoxImageContainer'
            });
            this.emptyAddButton = this.fileBoxImageContainer.createChild({
                tag: 'span',
                cls: 'AddText',
                html: this.emptyAddText,
                hidden: !!this.value
            });
            this.emptyAddButton.setVisibilityMode(Ext.Element.DISPLAY);
            if (!this.readOnly) {
                this.emptyAddButton.on('click', this.onAdd, this);
            }
            else {
                this.emptyAddButton.hide();
            }

            var src = this.getPlaceholder();
            if (this.value) {
                if (this.value.indexOf(';') === -1) {
                    src = Phlexible.Router.generate('mediamanager_media', {file_id: this.value, template_key: '/_mm_large'});
                }
                else {
                    var split = this.value.split(';');
                    src = Phlexible.Router.generate('mediamanager_media', {file_id: split[0], template_key: '/_mm_large', file_version: split[1]});
                }
            }
            this.fileBoxImage = this.fileBoxImageContainer.createChild({
                tag: 'img',
                cls: 'FileBoxImage',
                border: 0,
                src: src,
                height: 96,
                width: 96,
                qtip: this.fileTitle || ''
            });

            this.el.createChild({
                tag: 'div',
                cls: 'x-form-clear-left'
            });
        }
        if (this.tabIndex !== undefined) {
            this.el.dom.setAttribute('tabIndex', this.tabIndex);
        }

        this.el.addClass([this.fieldClass, this.cls]);
        this.initValue();
    },

    // private
    onDisable: function () {
        if (this.rendered) {
            this.el.mask();
        }
        Ext.form.FileField.superclass.onDisable.call(this);
    },

    // private
    onEnable: function () {
        if (this.rendered) {
            this.el.unmask();
        }
        Ext.form.FileField.superclass.onEnable.call(this);
    },

    initComponent: function () {
        Ext.form.FileField.superclass.initComponent.call(this);
        this.addEvents(
            /**
             * @event autosize
             * Fires when the autosize function is triggered.  The field may or may not have actually changed size
             * according to the default logic, but this event provides a hook for the developer to apply additional
             * logic at runtime to resize the field if needed.
             * @param {Ext.form.Field} this This text field
             * @param {Number} width The new field width
             */
            'autosize'
        );
    },

    // private
    initEvents: function () {
        Ext.form.FileField.superclass.initEvents.call(this);
        if (this.validationEvent !== false) {
            this.el.on(this.validationEvent, this.validate, this, {buffer: this.validationDelay});
        }
    },

    processValue: function (value) {
        if (this.stripCharsRe) {
            var newValue = value.replace(this.stripCharsRe, '');
            if (newValue !== value) {
                this.setRawValue(newValue);
                return newValue;
            }
        }
        return value;
    },

    filterValidation: function (e) {
        if (!e.isNavKeyPress()) {
            this.validationTask.delay(this.validationDelay);
        }
    },

    /**
     * Resets the current field value to the originally-loaded value and clears any validation messages.
     * Also adds emptyText and emptyClass if the original value was blank.
     */
    reset: function () {
        Ext.form.FileField.superclass.reset.call(this);
        this.applyEmptyText();
    },

    applyEmptyText: function () {
        if (this.rendered && this.getRawValue().length < 1) {
            this.el.addClass(this.emptyClass);
        }
    },

    // private
    preFocus: function () {
        this.el.removeClass(this.emptyClass);
    },

    // private
    postBlur: function () {
        this.applyEmptyText();
    },

    // private
    filterKeys: function (e) {
        var k = e.getKey();
        if (!Ext.isIE && (e.isNavKeyPress() || k == e.BACKSPACE || (k == e.DELETE && e.button == -1))) {
            return;
        }
        var c = e.getCharCode(), cc = String.fromCharCode(c);
        if (Ext.isIE && (e.isSpecialKey() || !cc)) {
            return;
        }
        if (!this.maskRe.test(cc)) {
            e.stopEvent();
        }
    },

    /**
     * Returns the raw data value which may or may not be a valid, defined value.  To return a normalized value see {@link #getValue}.
     * @return {Mixed} value The field value
     */
    getRawValue: function () {
        var v = this.rendered ? this.hiddenEl.getValue() : Ext.value(this.value, '');
        return v;
    },

    /**
     * Returns the normalized data value (undefined or emptyText will be returned as '').  To return the raw value see {@link #getRawValue}.
     * @return {Mixed} value The field value
     */
    getValue: function () {
        if (!this.rendered) {
            return this.value;
        }
        var v = this.hiddenEl.getValue();
        if (v === undefined) {
            v = '';
        }
        return v;
    },

    /**
     * Sets the underlying DOM field's value directly, bypassing validation.  To set the value with validation see {@link #setValue}.
     * @param {Mixed} value The value to set
     */
    setRawValue: function (v) {
        this.setValue(v);
        /*
         if(v !== undefined && v !== null && v !== '') {
         this.emptyAddButton.hide();
         var src;
         if (v.indexOf(';') === -1) {
         src = Phlexible.Router.generate('mediamanager_media', {file_id: id, template_key: '/_mm_large'});
         } else {
         var split = v.split(';');
         src = Phlexible.Router.generate('mediamanager_media', {file_id: split[0], template_key: '/_mm_large', file_version: split[1]});
         }
         this.fileBoxImage.dom.src = src;
         } else {
         this.emptyAddButton.show();
         this.fileBoxImage.dom.src = this.getPlaceholder();
         }
         */
        return this.hiddenEl.dom.value = (v === null || v === undefined ? '' : v);
    },

    /**
     * Sets a data value into the field and validates it.  To set the value directly without validation see {@link #setRawValue}.
     * @param {Mixed} value The value to set
     */
    setValue: function (v) {
        if (this.el && v !== undefined && v !== null && v !== '') {
            this.el.removeClass(this.emptyClass);
        }
        this.value = v;
        if (this.rendered) {
            if (v !== undefined && v !== null && v !== '') {
                this.emptyAddButton.hide();
                var src;
                if (v.indexOf(';') === -1) {
                    src = Phlexible.Router.generate('mediamanager_media', {file_id: v, template_key: '/_mm_large'});
                } else {
                    var split = v.split(';');
                    src = Phlexible.Router.generate('mediamanager_media', {file_id: split[0], template_key: '/_mm_large', file_version: split[1]});
                }
                src += '?_dc=' + (new Date().getTime());
                this.fileBoxImage.dom.src = src;
                if (this.value && !this.readOnly) {
                    this.removeButton.show();
                }
            } else {
                if (!this.readOnly) this.emptyAddButton.show();
                this.fileBoxImage.dom.src = this.getPlaceholder();
            }
            this.hiddenEl.dom.value = (v === null || v === undefined ? '' : v);
            this.validate();
        }
        this.applyEmptyText();
    },

    /**
     * Validates a value according to the field's validation rules and marks the field as invalid
     * if the validation fails
     * @param {Mixed} value The value to validate
     * @return {Boolean} True if the value is valid, else false
     */
    validateValue: function (value) {
        if (value.length < 1) { // if it's blank
            if (this.allowBlank) {
                this.clearInvalid();
                return true;
            } else {
                this.markInvalid(this.blankText);
                return false;
            }
        }
        if (!this.file_id) { // if it's blank
            if (this.allowBlank) {
                this.clearInvalid();
                return true;
            } else {
                this.markInvalid(this.blankText);
                return false;
            }
        }
        if (typeof this.validator == "function") {
            var msg = this.validator(value);
            if (msg !== true) {
                this.markInvalid(msg);
                return false;
            }
        }
        return true;
    }
});
