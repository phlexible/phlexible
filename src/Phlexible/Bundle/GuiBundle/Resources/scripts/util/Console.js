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
Phlexible.gui.util.Console = function(){
    // private
    var enabled = true;

    // private
    var xcall = function(method, args){
        if (enabled && typeof window.console === 'object' && typeof window.console[method] === 'function') {
            window.console[method].apply(window.console, args);
        }
    };

    // Enable / disable loggin
    this.enable = function() { enabled = true; };
    this.disable = function() { enabled = false; };

    // console wrapper
    this.clear =          function() { var m = 'clear';          xcall(m, arguments); };
    this.log =            function() { var m = 'log';            xcall(m, arguments); };
    this.debug =          function() { var m = 'debug';          xcall(m, arguments); };
    this.info =           function() { var m = 'info';           xcall(m, arguments); };
    this.warn =           function() { var m = 'warn';           xcall(m, arguments); };
    this.error =          function() { var m = 'error';          xcall(m, arguments); };
    this.assert =         function() { var m = 'assert';         xcall(m, arguments); };
    this.dir =            function() { var m = 'dir';            xcall(m, arguments); };
    this.dirxml =         function() { var m = 'dirxml';         xcall(m, arguments); };
    this.trace =          function() { var m = 'trace';          xcall(m, arguments); };
    this.group =          function() { var m = 'group';          xcall(m, arguments); };
    this.groupCollapsed = function() { var m = 'groupCollapsed'; xcall(m, arguments); };
    this.groupEnd =       function() { var m = 'groupEnd';       xcall(m, arguments); };
    this.time =           function() { var m = 'time';           xcall(m, arguments); };
    this.timeEnd =        function() { var m = 'timeEnd';        xcall(m, arguments); };
    this.profile =        function() { var m = 'profile';        xcall(m, arguments); };
    this.profileEnd =     function() { var m = 'profileEnd';     xcall(m, arguments); };
    this.count =          function() { var m = 'count';          xcall(m, arguments); };
};

Phlexible.console = new Phlexible.gui.util.Console();
