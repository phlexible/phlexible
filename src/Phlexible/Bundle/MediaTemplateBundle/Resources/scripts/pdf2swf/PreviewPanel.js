Phlexible.mediatemplates.pdf2swf.PreviewPanel = Ext.extend(Phlexible.mediatemplates.BasePreviewPanel, {
    getCreateUrl: function () {
        return Phlexible.Router.generate('mediatemplates_preview_pdf');
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
        return {
            tag: 'embed',
            src: Phlexible.Router.generate('mediatemplates_preview_get', {file: data.file, dc: new Date().getTime()}),
            width: 400,
            height: 500,
            wmode: 'transparent',
            type: data.mimetype
        };
    }
});

Ext.reg('mediatemplates-pdf2swfpreviewpanel', Phlexible.mediatemplates.pdf2swf.PreviewPanel);