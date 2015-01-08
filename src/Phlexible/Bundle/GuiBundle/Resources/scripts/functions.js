/**
 * @param {String} path
 * @returns {String}
 */
Phlexible.url = function (path) {
    return Phlexible.baseUrl + path;
};

/**
 * @param {String} path
 * @returns {String}
 */
Phlexible.path = function (path) {
    return Phlexible.basePath + path;
};

/**
 * @param {String} path
 * @returns {String}
 */
Phlexible.bundleAsset = function (path) {
    return Phlexible.bundlePath + path;
};

/**
 * Return an arbitrary value as a string representation
 *
 * @param {*}     arr            Value
 * @param {Array} skipFunctions  Functions to skip
 * @param {Array} skipObjectKeys Object keys to skip
 */
Phlexible.dump = function (arr, skipFunctions, skipObjectKeys) {
    if (!skipObjectKeys) {
        skipObjectKeys = [];
    }
    return Phlexible.dumpRep(arr, 0, '', skipFunctions, skipObjectKeys, false);
};
/**
 * Return an arbitrary value as a html representation
 *
 * @param {*}     arr            Value
 * @param {Array} skipFunctions  Functions to skip
 * @param {Array} skipObjectKeys Object keys to skip
 */
Phlexible.dumpHtml = function (arr, skipFunctions, skipObjectKeys) {
    if (!skipObjectKeys) {
        skipObjectKeys = [];
    }
    return Phlexible.dumpRep(arr, 0, '', skipFunctions, skipObjectKeys, true);
};

/**
 * Reutrn an arbitrary value as a string representation
 * @param {*}       v              Value
 * @param {Number}  level          Level
 * @param {String}  lastPadding    Last padding
 * @param {Array}   skipFunctions  Functions to skip
 * @param {Array}   skipObjectKeys Object keys to skip
 * @param {Boolean} html           As Html?
 */
Phlexible.dumpRep = function (v, level, lastPadding, skipFunctions, skipObjectKeys, html) {
    var dump = '';
    if (!level) level = 0;
    if (!lastPadding) last_padding = '';

    // The padding given at the beginning of the line.
    var levelPadding = "";
    for (var j = 1; j < level + 1; j++) {
        levelPadding += '    ';
    }

    switch (typeof(v)) {
        case 'object':
            var sub1 = '', sub2, value;
            for (var key in v) {
                if (!v.hasOwnProperty(key)) {
                    continue;
                }
                if (skipObjectKeys.indexOf(key) === -1) {
                    value = v[key];
                    sub2 = Phlexible.dumpRep(value, level + 1, levelPadding, skipFunctions, skipObjectKeys, html);
                }
                else {
                    sub2 = '';
                    if (html) sub2 += '<span style="color: red">';
                    sub2 += '(skipped)';
                    if (html) sub2 += '</span>';
                }

                if (sub2 !== false) {
                    sub1 += (sub1 ? ',' : '') + "\n" + levelPadding;
                    if (html) sub1 += '<span style="color: blue;">';
                    sub1 += key + ':';
                    if (html) sub1 += '</span>';
                    sub1 += ' ' + sub2;
                }
            }
            if (sub1) {
                if (level) dump += '{';
                dump += sub1;
                if (level) dump += "\n" + lastPadding + "}";
            } else {
                if (html) dump += '<span style="color: red">';
                dump += '(empty)';
                if (html) dump += '</span>';
            }
            break;

        case 'function':
            if (skipFunctions) {
                return false;
            }
            dump = '';
            if (html) dump += '<span style="color: red;">';
            dump += '(function)';
            if (html) dump += '</span>';
            break;

        case 'boolean':
            dump = '';
            if (html) dump += '<span style="color: green;">';
            dump += (v ? 'true' : 'false');
            if (html) dump += '</span>';
            dump += ' ';
            if (html) dump += '<span style="color: red;">';
            dump += '(boolean)';
            if (html) dump += '</span>';
            break;

        case 'number':
            dump = '';
            if (html) dump += '<span style="color: green;">';
            dump += v;
            if (html) dump += '</span>';
            dump += ' ';
            if (html) dump += '<span style="color: red;">';
            dump += '(' + typeof(v) + ')';
            if (html) dump += '</span>';
            break;

        default:
            dump = '';
            if (html) dump += '<span style="color: green;">';
            dump += '"' + v + '"';
            if (html) dump += '</span>';
            dump += ' ';
            if (html) dump += '<span style="color: red;">';
            dump += '(' + typeof(v) + ')';
            if (html) dump += '</span>';
            break;
    }

    //dump += "\n";

    return dump;
};

Ext.require('Ext.ux.Notification');

/**
 * Display a message onscreen
 *
 * @param {String} title       Title of the message
 * @param {String} text        Text of the message
 * @param {Object} extraParams Extra parameters
 */
Phlexible.msg = function (title, text, extraParams) {
    var params = Ext.apply({
        iconCls: 'p-gui-msg_information-icon',
        title: title,
        html: text,
        autoDestroy: true,
        hideDelay: 5000
    }, extraParams);
    new Ext.ux.Notification(params).show(document);
};

/**
 * Shortcut for success messages
 *
 * @param {String} text
 */
Phlexible.success = function (text) {
    Phlexible.msg(Phlexible.gui.Strings.success, text, {});
};

/**
 * Shortcut for failure messages
 *
 * @param {String} text
 */
Phlexible.failure = function (text) {
    //Phlexible.msg(Phlexible.gui.Strings.error, text, {
    //    iconCls: 'p-gui-msg_error-icon'
    //});
    Ext.MessageBox.show({
        title: Phlexible.gui.Strings.error,
        msg: text,
        buttons: Ext.MessageBox.OK,
        icon: Ext.MessageBox.ERROR
    });
};

/**
 * Output error
 */
Phlexible.error = function () {
    var response = null, result = null;
    Ext.each(arguments, function (obj) {
        if (obj.responseText) {
            response = obj;
            return false;
        }
    });
    if (!response) {
        Ext.MessageBox.alert('Authorisation Error', 'The last request resulted in an Authorisation error. This could be the result of an expired session.');
        return;
    }

    try {
        result = Ext.decode(response.responseText);
    }
    catch (err) {
    }

    if (!result || !result.msg) {
        Ext.MessageBox.alert('Authorisation Error', 'The last request resulted in an Authorisation error. This could be the result of an expired session.');
        return;
    }

    var config = {
        title: 'Status Code: ' + response.status,
        iconCls: 'p-gui-msg_error-icon',
        width: 900,
        height: 400,
        modal: true,
        resizable: false,
        contrainHeader: true,
        bodyStyle: 'padding: 10px; background-color: #DFE8F6;',
        layout: 'fit',
        items: [
            {
                border: false,
                autoScroll: true,
                items: []
            }
        ]
    };

    if (result.data.message && result.msg != result.data.message) {
        config.items[0].items.push({
            bodyStyle: 'font-size: 15px; font-weight: bold;',
            border: false,
            html: result.data.message
        });
        config.items[0].items.push({
            bodyStyle: 'font-size: 13px; padding-top: 5px;',
            border: false,
            html: result.msg
        });
    }
    else {
        config.items[0].items.push({
            bodyStyle: 'font-size: 15px; font-weight: bold;',
            border: false,
            html: result.msg
        });
    }

    if (result.data.stacktrace) {
        config.items[0].items.push({
            bodyStyle: 'font-size: 11px; font-weight: bold; padding-top: 20px; padding-bottom: 3px',
            border: false,
            html: '<div>Stack trace:</div>'
        });
        config.items[0].items.push({
            xtype: 'textarea',
            border: false,
            value: result.data.stacktrace,
            width: 860,
            height: 265
        });
    }

    var w = new Ext.Window(config);
    w.show();
};
Phlexible.getStackTrace = function (delimiter) {
    var callstack = [];
    var isCallstackPopulated = false;
    try {
        yea.i.dont.exist += 0; // doesn't exist - that's the point
    } catch (e) {
        var lines, i, len;
        if (e.stack) { //Firefox
            lines = e.stack.split("\n");
            for (i = 0, len = lines.length; i < len; i++) {
//        if (lines[i].match(/^\s*[A-Za-z0-9\-_\$]+\(/)) {
                callstack.push(lines[i]);
//        }
            }
            //Remove call to printStackTrace()
            callstack.shift();
            isCallstackPopulated = true;
        }
        else if (window.opera && e.message) { //Opera
            lines = e.message.split("\n");
            for (i = 0, len = lines.length; i < len; i++) {
                if (lines[i].match(/^\s*[A-Za-z0-9\-_\$]+\(/)) {
                    var entry = lines[i];
                    //Append next line also since it has the file info
                    if (lines[i + 1]) {
                        entry += " at " + lines[i + 1];
                        i++;
                    }
                    callstack.push(entry);
                }
            }
            //Remove call to printStackTrace()
            callstack.shift();
            isCallstackPopulated = true;
        }
    }
    if (!isCallstackPopulated) { //IE and Safari
        var currentFunction = arguments.callee.caller;
        while (currentFunction) {
            var fn = currentFunction.toString();
            var fname = fn.substring(fn.indexOf("function") + 8, fn.indexOf("(")) || "anonymous";
            callstack.push(fname);
            currentFunction = currentFunction.caller;
        }
    }
    if (delimiter) {
        return callstack.join(delimiter);
    }
    return callstack;
};

/**
 * Clone object
 *
 * @param {*} obj
 * @returns {*}
 */
Phlexible.clone = function (obj) {
    var myNewObj, i;

    if (Ext.type(obj) == 'object') {
        if (obj === null)
            return obj;

        myNewObj = {};

        for (i in obj) {
            if (!obj.hasOwnProperty(i)) {
                continue;
            }
            myNewObj[i] = Phlexible.clone(obj[i]);
        }
    } else if (Ext.type(obj) == 'array') {
        if (obj === null)
            return obj;

        myNewObj = [];

        for (i = 0; i < obj.length; i++) {
            myNewObj.push(Phlexible.clone(obj[i]));
        }
    } else {
        return obj;
    }

    return myNewObj;
};

/**
 * Evaluate string to reference
 *
 * @param {String} s
 * @returns {*}
 */
Phlexible.evalClassString = function (s) {
    var a = s.split('.');
    var n = window;
    for (var i = 0; i < a.length; i++) {
        if (!n[a[i]]) return false;
        n = n[a[i]];
    }

    return n;
};

/**
 * Create inline icon
 *
 * @param {String} iconCls
 * @param {Object} attr
 * @returns {string}
 */
Phlexible.inlineIcon = function (iconCls, attr) {
    if (!attr) attr = {};

    attr = Ext.applyIf(attr, {
        src: Ext.BLANK_IMAGE_URL,
        width: 16,
        height: 16,
        'class': 'p-inline-icon ' + iconCls
    });

    var s = '<img';
    for (var i in attr) {
        if (!attr.hasOwnProperty(i)) {
            continue;
        }
        s += ' ' + i + '="' + attr[i] + '"';
    }

    s += ' />';

    return s;
};
