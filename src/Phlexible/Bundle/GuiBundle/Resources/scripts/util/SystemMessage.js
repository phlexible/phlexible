/**
 * @class Phlexible.gui.util.SystemMessage
 * @extends Ext.util.Observable
 * This class represents the menu bar
 * <br><br>Usage:<pre><code>
 var msg = new Phlexible.gui.util.SystemMessage({
     delayInterval: 3000,
     pollInterval: 20000
 });
 // load menu
 msg.startPoll();
 * </code></pre>
 * @constructor
 * @param {Object} config A config object that sets properties.
 */
Phlexible.gui.util.SystemMessage = function(config){
    this.addEvents({
        /**
         * @event message
         * Fires when a new message arrives.
         * @param {Object} message
         */
        "message": true
    });

    if(!config) config = {};
    this.pollBtn = null;
    if(config.noButton) this.noButton = true;

    Phlexible.gui.util.SystemMessage.superclass.constructor.call(this, config);
};

Ext.extend(Phlexible.gui.util.SystemMessage, Ext.util.Observable, {
    interval: 30000,
    pollTimeout: 30,

    task: null,

    noButton: false,

    getButton: function() {
        if(this.noButton) return false;

        if(!this.pollBtn) {
            this.pollBtn = Phlexible.Frame.taskbar.trayPanel.add({
                id: 'update',
                cls: 'x-btn-icon',
                iconCls: 'p-gui-update_inactive-icon',
                handler: this.start,
                scope: this
            });
        }

        return this.pollBtn;
    },

    getTask: function() {
        return {
            run: this.poll,
            interval: this.interval,
            scope: this
        };
    },

    poll: function() {
        Ext.Ajax.request({
            url: Phlexible.Router.generate('messages_poll'),
            params: {
                dc: (new Date().getTime())
            },
            method: 'post',
            scope: this,
            success: this.processResponse,
            failure: function(response, options) {
                Phlexible.console.log(response);
                Phlexible.console.log(options);
            }
        });
    },

    /**
     * Activate message system
     */
    activate: function(){
        if (!this.noButton) {
            btn = this.getButton();
            btn.setIconClass('p-gui-update_active-icon');
            btn.handler = this.stop;
        }
    },

    /**
     * Deactivate message system
     */
    deactivate: function(){
        if (!this.noButton) {
            btn = this.getButton();
            btn.setIconClass('p-gui-update_inactive-icon');
            btn.handler = this.start;
        }
    },

    /**
     * Start message system with delay
     */
    start: function(){
        this.stop();

        this.activate();

        this.task = this.getTask();
        Ext.TaskMgr.start(this.task);
    },

    /**
     * Stop message system
     */
    stop: function() {
        if (this.task) {
            Ext.TaskMgr.stop(this.task);

            this.deactivate();
            this.task = null;
        }

    },

    /**
     * Process message response
     */
    processResponse: function(response){
        if(!this.noButton) Ext.fly(this.getButton().getEl()).frame('#8db2e3', 1);
        if(response.responseText) {
            var events = Ext.decode(response.responseText);
            for(var i=0; i<events.length; i++){
                this.fireEvent('message', events[i]);
            }
        }
    }
});
