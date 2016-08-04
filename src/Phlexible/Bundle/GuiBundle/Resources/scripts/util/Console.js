/*global Ext, Phlexible*/
Ext.provide('Phlexible.gui.util.Console');

/*
 * Phlexible
 * Copyright(c) 2006-2010, brainbits GmbH
 * sw@brainbits.net
 *
 * http://phlexible.net/license
 */

/**
 * @class Phlexible.util.Console
 * Console logging wrapper. Supports Gecko/Firebug and Webkit based Browsers
 * For a complete list of logging methods see @see http://getfirebug.com/wiki/index.php/Console_API
 * Example usage:
 * <pre><code>
 Phlexible.console = new Phlexible.util.Console();

 // Write a simple log message to the console
 Phlexible.console.log('Hello World!');
 // will output
 // > Hello World!

 // Write multiple values to console
 Phlexible.console.info(1,2,3)
 // will output
 // > 1 2 3

 // Disable logging
 Phlexible.console.disable();

 // Enable logging
 Phlexible.console.enable();
 </code></pre>
 * @constructor
 * (defaults to 10)
 */
Phlexible.gui.util.Console = function () {
    "use strict";
    // private
    var self = this;
    var enabled = true;

    // Enable / disable logging
    self.enable = function () {
        enabled = true;
    };
    self.disable = function () {
        enabled = false;
    };

    Ext.each([
        'clear',
        'log',
        'debug',
        'info',
        'warn',
        'error',
        'assert',
        'dir',
        'dirxml',
        'trace',
        'group',
        'groupCollapsed',
        'groupCollapsed',
        'time',
        'timeEnd',
        'profile',
        'profileEnd',
        'count'
    ], function(methodName) {
        if (enabled && typeof window.console === 'object' && typeof window.console[methodName] === 'function') {
            if (Function.prototype.bind) {
                self[methodName] = Function.prototype.bind.call(window.console[methodName], window.console);
            } else {
                self[methodName] = function() {
                    Function.prototype.apply.call(window.console[methodName], window.console, arguments);
                };
            }
        } else {
            self[methodName] = function() {};
        }
    });
};
