/**
 * @class Phlexible.LoadHandler
 * Provides loading handlers for menus.<br><br>
 * Extended attributes are needed to call a handler.
 * <br><br>Usage:<pre><code>
 Phlexible.LoadHandler.handlerPanel({
...
});
 * </code></pre>
 * <b>Below are common extended attributes that can be passed to any handler.</b>
 * @cfg {Function} handler A {@link Phlexible.LoadHandler} method
 * <b>Below are extended attributes that can be used for handleLink, handleMenu and handleDialog.</b>
 * @cfg {string} action An action target for the handler
 * @cfg {string} text An text that is displayed
 * <b>Below are extended attributes that can be used for handleIframe.</b>
 * @cfg {string} iframeUrl The url for the Iframe
 * @cfg {string} iframeTitle A title for the Iframe
 */
Phlexible.LoadHandler = {

    /**
     * Called to handle a direct link. Will leave MAKEweb Frame.
     * @param {Ext.Item} menu The {@link Ext.menu.Item} object
     * @param {Ext.Eventobject} event The {@link Ext.EventObject} object
     */
    handleLink: function(menu, event){
        if(!menu.handleTarget || !menu.handleTarget.href){
            return;
        }

        if (menu.handleTarget.hrefTarget == '_blank') {
            window.open(menu.handleTarget.href);
        }
        else {
            top.document.location.href = menu.handleTarget.href;
        }
    },

    /**
     * Called to load an url into an iframe.
     * @param {Ext.Item} menu The {@link Ext.menu.Item} object
     * @param {Ext.Eventobject} event The {@link Ext.EventObject} object
     */
    handleIframe: function(menu, event){
        if(!menu.handleTarget || !menu.handleTarget.iframeurl){
            return;
        }

        var url = decodeURIComponent(menu.handleTarget.iframeurl);

        var config = {
            title: menu.handleTarget.iframetitle || url,
            closable: true,
            closeable: true,
            defaultSrc: url
        };

        if(menu.handleTarget.iframeiconclass) {
            config.iconCls = menu.handleTarget.iframeiconclass;
        }

        var iframePanel = new Ext.ux.ManagedIframePanel(config);

        Phlexible.Frame.mainPanel.add(iframePanel);
        Phlexible.Frame.taskbar.taskButtonPanel.add(iframePanel);
        Phlexible.Frame.mainPanel.getLayout().setActiveItem(iframePanel);
        Phlexible.Frame.viewport.doLayout();
    },

    /**
     * Menu handler to load a Dialog.
     * @param {Ext.Item} menu The {@link Ext.menu.Item} object
     * @param {Ext.Eventobject} event The {@link Ext.EventObject} object
     */
    handleDialog: function(menu, event){
        var handleTarget;
        var handleParams;

        if(typeof menu == 'string'){
            handleTarget = Phlexible.evalClassString(menu);
            handleParams = {};
        } else {
            if(!menu.handleTarget){
                alert('no dialog name given!');
                return;
            }
            handleTarget = Phlexible.evalClassString(menu.handleTarget);
            handleParams = menu.params || {};
        }

        if(handleTarget) {
            var o = new handleTarget(handleParams);
            o.show();
        }
    },

    /**
     * Menu handler to execute a Function.
     * @param {Ext.Item} menu The {@link Ext.menu.Item} object
     * @param {Ext.Eventobject} event The {@link Ext.EventObject} object
     */
    handleFunction: function(menu, event) {
        var handleTarget;
        var handleParams;

        if(typeof menu == 'string'){
            handleTarget = Phlexible.evalClassString(menu);
            handleParams = {};
        } else {
            if(!menu.handleTarget){
                alert('no function name given!');
                return;
            }
            handleTarget = Phlexible.evalClassString(menu.handleTarget);
            handleParams = menu.params || {};
        }

        handleTarget(handleParams);
    },

    /**
     * Menu handler to load a Panel.
     * @param {Ext.Item} menu The {@link Ext.menu.Item} object
     * @param {Ext.Eventobject} event The {@link Ext.EventObject} object
     */
    handleClass: function(menu, event){
        alert("handleClass call, aborted");
        return;

        var handleClass;
        var handleClassName;

        if(typeof menu == 'string'){
            handleClassName = menu.replace(/\./g, '_');
            handleClass = Phlexible.evalClassString(menu);
            handleParams = {};
        } else {
            if(!menu.handleClassName) {
                alert('no class name given!');
                return;
            }
            handleClass = Phlexible.evalClassString(menu.handleTarget);
            handleClassName = menu.handleClassName.replace(/\./g, '_');
            handleParams = menu.params || {};
        }

        Phlexible.Frame.getMainPanel().loadPanel(handleClassName, handleClass, handleParams);
    },

    /**
     * Menu handler to load a Panel.
     * @param {Ext.Item} menu The {@link Ext.menu.Item} object
     * @param {Ext.EventObject} event The {@link Ext.EventObject} object
     */
    handlePanel: function(menu, event) {
        var handlePanel;
        var handlePanelIdentifier;
        var handleParams = {};

        if(typeof menu == 'string'){
            handlePanelIdentifier = menu.replace(/\./g, '_');
            handlePanel = Phlexible.evalClassString(menu);
        } else {
            if(!menu.handleTarget) {
                alert('no panel name given!');
                return;
            }
            handlePanel = Phlexible.evalClassString(menu.handleTarget);
            if (menu['identifier']) {
                handlePanelIdentifier = menu['identifier'];
            } else {
                handlePanelIdentifier = menu.handleTarget;
            }
            handlePanelIdentifier = handlePanelIdentifier.replace(/\./g, '_');
            if(menu.params) {
                handleParams = menu.params;
            }
        }

        Phlexible.Frame.loadPanel(handlePanelIdentifier, handlePanel, handleParams);
    }

};
