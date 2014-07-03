Phlexible.mediatemplates.video.PreviewPanel = Ext.extend(Phlexible.mediatemplates.BasePreviewPanel, {
    getCreateUrl: function () {
        return Phlexible.Router.generate('mediatemplates_preview_video');
    },

    getResult: function (data) {
        var s = '';
        if (data.template) {
            s += data.template;
        }
        if (data.width && data.height) {
            s += ', ' + data.width + ' x ' + data.height;
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
            tag: 'video',
            autoplay: 'autoplay',
            controls: 'controls',
            width: data.width,
            height: data.height,
            children: [
                {
                    tag: 'source',
                    src: Phlexible.Router.generate('mediatemplates_preview_get', {file: data.file, dc: new Date().getTime()}),
                    type: data.mimetype
                }
            ]
            /*
             tag: 'embed',
             src: Phlexible.component('/mediamanager/flash/player.swf'),
             width: parseInt(data.width, 10),
             height: parseInt(data.height, 10) + 20,
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

Ext.reg('mediatemplates-videopreviewpanel', Phlexible.mediatemplates.video.PreviewPanel);