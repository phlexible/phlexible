Ext.namespace('Phlexible.mediatemplates.audio');

Phlexible.mediatemplates.audio.PreviewPanel = Ext.extend(Phlexible.mediatemplates.BasePreviewPanel, {
    getCreateUrl: function () {
        return Phlexible.Router.generate('mediatemplates_preview_audio');
    },

    getResult: function (data) {
        var s = '';
        if (data.template) {
            s += data.template;
        }
        if (data.format) {
            s += ', ' + data.format;
        }
        if (data.size) {
            s += ', ' + Phlexible.Format.size(data.size);
        }
        return s;
    },

    getPreviewDomHelperConfig: function (data) {
        //var link = 'file=' + Phlexible.Router.generate('mediatemplates_preview_get', {file: data.file, dc: new Date().getTime()});

        return {
            tag: 'audio',
            autoplay: 'autoplay',
            controls: 'controls',
            children: [
                {
                    tag: 'source',
                    src: Phlexible.Router.generate('mediatemplates_preview_get', {file: data.file, dc: new Date().getTime()}),
                    type: data.mimetype
                }
            ]
            /*
             src: Phlexible.bundleAsset('/mediamanager/flash/player.swf'),
             width: 300,
             height: 20,
             allowfullscreen: 'false',
             allowscriptaccess: 'always',
             quality: 'high',
             wmode: 'transparent',
             flashvars: link,
             type: 'application/x-shockwave-flash'
             */
        };
    }
});

Ext.reg('mediatemplates-audiopreviewpanel', Phlexible.mediatemplates.audio.PreviewPanel);