Ext.namespace(
    'Phlexible.gui.menuhandle.handle',
    'Phlexible.gui.util'
);

xonerror=function(msg, url, l, x) {
    // workaround for FF3.5
//    if(msg == "Permission denied to access property 'dom' from a non-chrome context") return;
    var stackTraceOutput;
    try {
        var stackTrace = Phlexible.getStackTrace();
        stackTraceOutput = 'Stacktrace:<br /><ol>';
        for(var i=0; i<stackTrace.length; i++) {
            stackTraceOutput += '<li>' + stackTrace[i] + '</li>';
        }
        stackTraceOutput += '</ol>';
    } catch(e) {
        stackTraceOutput = 'No stacktrace.';
    }
    var w = new Ext.Window({
        title: Phlexible.gui.Strings.error_occured,
        width: 500,
        height: 300,
        modal: true,
        layout: 'border',
        items: [{
            xtype: 'panel',
            region: 'north',
            height: 80,
            bodyStyle: 'padding: 5px;',
            html: 'Error: <b>' + msg + '</b><br />' +
                  '<br />' +
                  'File: ' + url + ' (' + l + ')'
        },{
            xtype: 'panel',
            region: 'center',
            bodyStyle: 'padding: 5px;',
            autoScroll: true,
            html: stackTraceOutput
        }]
    });
    w.show();
};
