/*jsl:ignoreall*/
//Ext.ux.HTMLEditorImage
//a plugin to handle images in the Ext.ux.HtmlEditor
Ext.ux.HTMLEditorImage = function () {

    // PRIVATE

    // pointer to Ext.ux.HTMLEditor
    var editor;

    // pointer to Ext.Window
    var win;

    // pointer to Ext.FormPanel
    var tabs;

    // pointer to Ext.ux.ImageBrowser
    var imageBrowser;

    // other private variables
    var constrained = false;
    var originalWidth = 0;
    var originalHeight = 0;

    // return the selected image (if an image is selected)
    var getSelectedImage = function () {

        if (Ext.isIE) {

            // ie specific code
            return function () {
                var selection = editor.doc.selection;
                if (selection.type == "Control") {
                    var element = selection.createRange()(0);
                    if (element.nodeName.toLowerCase() == 'img') {
                        return element;
                    }
                }
                return null;
            }

        } else {

            // firefox specific code
            return function () {
                var selection = editor.win.getSelection();
                if (selection.focusOffset == selection.anchorOffset + 1) {
                    var element = selection.focusNode.childNodes[selection.focusOffset - 1];
                    if (element.nodeName.toLowerCase() == 'img') {
                        return element;
                    }
                }
                return null;
            }
        }
    }();

    // set image details to data passed from image browser
    var setImageDetails = function (data) {
        tabs.form.findField('src').setValue(data.url);
        tabs.form.findField('alt').setValue(data.name);
        tabs.form.findField('width').setValue(data.width);
        tabs.form.findField('height').setValue(data.height);
        tabs.form.findField('constrain').setValue(true);
        sourceChanged();
    };

    // create new image node from image details
    var createImage = function () {
        var element = document.createElement("img");
        element.src = tabs.form.findField('src').getValue();
        element.alt = tabs.form.findField('alt').getValue();
        element.style.width = tabs.form.findField('width').getValue() + "px";
        element.style.height = tabs.form.findField('height').getValue() + "px";
        return element;
    }

    // insert the image into the editor (browser-specific)
    var insertImageByBrowser = function () {

        if (Ext.isIE) {

            // ie-specific code
            return function () {

                // get selected text/range
                var selection = editor.doc.selection;
                var range = selection.createRange();

                // insert the image over the selected text/range
                range.pasteHTML(createImage().outerHTML);
            };

        } else {

            // firefox-specific code
            return function () {

                // get selected text/range
                var selection = editor.win.getSelection();

                // delete selected text/range
                if (!selection.isCollapsed) {
                    selection.deleteFromDocument();
                }

                // insert the image
                selection.getRangeAt(0).insertNode(createImage());
            };
        }
    }();

    // insert the image into the editor
    var insertImage = function () {

        // focus on the editor to regain selection
        editor.win.focus();

        // insert the image
        insertImageByBrowser();

        // perform required toolbar operations
        editor.updateToolbar();
        editor.deferFocus();
    };

    // enable insert button when image source has been entered
    var sourceChanged = function () {
        var disabled = (tabs.form.findField('src').getValue() == "");
        Ext.getCmp('insert-btn').setDisabled(disabled);
    };

    // if constraining size ratio then adjust height if width changed
    var widthChanged = function () {
        if (constrained) {
            tabs.form.findField('height').setValue(
                Math.round(tabs.form.findField('width').getValue()
                    / originalWidth * originalHeight)
            );
        }
    };

    // if constraining size ratio then adjust width if height changed
    var heightChanged = function () {
        if (constrained) {
            tabs.form.findField('width').setValue(
                Math.round(tabs.form.findField('height').getValue()
                    / originalHeight * originalWidth)
            );
        }
    };

    // record original image size when constrain is checked
    var constrain = function (checkbox, checked) {
        constrained = checked;
        if (constrained) {
            originalWidth = tabs.form.findField('width').getValue();
            originalHeight = tabs.form.findField('height').getValue();
            if (!originalWidth || !originalHeight) {
                checkbox.setValue(false);
            }
        }
    };

    // open/show the image details window
    var openImageWindow = function () {

        if (!win) {

            // create Ext.FormPanel if not previously created
            tabs = new Ext.FormPanel({
                labelWidth: 70,
                width: 350,
                items: {
                    xtype: 'tabpanel',
                    border: false,
                    activeTab: 0,
                    bodyStyle: 'padding:5px',
                    defaults: {autoHeight: true},
                    items: [
                        {
                            xtype: 'fieldset',
                            border: true,
                            title: 'General',
                            autoHeight: true,
                            defaults: {width: 270},
                            items: [
                                {
                                    xtype: 'trigger',
                                    fieldLabel: 'Source',
                                    triggerClass: 'x-form-search-trigger',
                                    name: 'src',
                                    allowBlank: false,
                                    listeners: {
                                        'change': {fn: sourceChanged, scope: this}
                                    },
                                    onTriggerClick: function () {
                                        if (!imageBrowser) {
                                            imageBrowser = new Ext.ux.ImageBrowser({
                                                width: 514,
                                                height: 321,

                                                // for a live environment, replace this call
                                                // with a server script, such as...
                                                // listURL: 'listimages.php',
                                                listURL: 'imagelist.txt',

                                                // these are also example php scripts
                                                uploadURL: 'uploadimage.php',
                                                deleteURL: 'deleteimage.php',

                                                // set the callback from the image browser
                                                callback: setImageDetails
                                            });
                                        }
                                        imageBrowser.show();
                                    }
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Description',
                                    name: 'alt'
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Title',
                                    name: 'title'
                                },
                                {
                                    xtype: "numberfield",
                                    fieldLabel: 'Width',
                                    name: 'width',
                                    width: 50,
                                    allowDecimals: false,
                                    allowNegative: false,
                                    listeners: {
                                        'change': {fn: widthChanged, scope: this}
                                    }
                                },
                                {
                                    xtype: "numberfield",
                                    fieldLabel: 'Height',
                                    name: 'height',
                                    width: 50,
                                    allowDecimals: false,
                                    allowNegative: false,
                                    listeners: {
                                        'change': {fn: heightChanged, scope: this}
                                    }
                                },
                                {
                                    xtype: "checkbox",
                                    hideLabel: true,
                                    boxLabel: "Constrain Proportions",
                                    name: 'constrain',
                                    checked: false,
                                    listeners: {
                                        'check': {fn: constrain, scope: this}
                                    }
                                }
                            ]
                        }
                    ]
                }
            });

            // create Ext.Window if not previously created
            win = new Ext.Window({
                title: 'Insert/Edit Image',
                closable: true,
                modal: true,
                closeAction: 'hide',
                width: 400,
                height: 350,
                plain: true,
                layout: 'fit',
                border: false,
                items: tabs,
                buttons: [
                    {
                        text: 'Insert',
                        id: 'insert-btn',
                        disabled: true,
                        handler: function () {
                            win.hide();
                            insertImage();
                        }
                    },
                    {
                        text: 'Close',
                        handler: function () {
                            win.hide();
                        }
                    }
                ],
                listeners: {
                    'show': function () {
                        tabs.form.reset();
                        var element = getSelectedImage();
                        if (element) {

                            // still working on this!!!
                            // need to fix image source as it is changed
                            // from a relative url to an absolute url
                            tabs.form.findField('src').setValue(element.src);
                            tabs.form.findField('alt').setValue(element.alt);
                            tabs.form.findField('width').setValue(element.style.width);
                            tabs.form.findField('height').setValue(element.style.height);
                            tabs.form.findField('constrain').setValue(true);
                        }
                    }
                }
            });
        }

        // show the window
        win.show(this);
    }

    // PUBLIC

    return {

        // Ext.ux.HTMLEditorImage.init
        // called upon instantiation
        init: function (htmlEditor) {
            editor = htmlEditor;

            // append the insert image icon to the toolbar
            editor.tb.insertToolsAfter('createlink', {
                itemId: 'image',
                cls: 'x-btn-icon x-edit-image',
                handler: openImageWindow,
                scope: this,
                clickEvent: 'mousedown',
                tooltip: {
                    title: 'Image',
                    text: 'Insert/edit an image.',
                    cls: 'x-html-editor-tip'
                }
            });
        }
    }
}
