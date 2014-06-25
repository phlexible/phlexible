Phlexible.mediamanager.FilePreviewPanel = Ext.extend(Ext.Panel, {
    strings: Phlexible.mediamanager.Strings,
    title: Phlexible.mediamanager.Strings.preview,
    cls: 'p-mediamanager-preview-panel',
    height: 270,

    file_id: null,
    file_version: null,

    // private
    initComponent: function() {
        if (this.file_id && this.file_version && this.file_name && this.document_type_key && this.cache) {
            this.html = this.getHtml(this.file_id, this.file_version, this.file_name, this.document_type_key, this.asset_type, this.cache);
        }
        else {
            this.html = this.createNoPreview();
        }

        Phlexible.mediamanager.FilePreviewPanel.superclass.initComponent.call(this);
    },

    loadRecord: function(r) {
        var file_id = r.get('id');
        var file_name = r.get('name');
        var file_version = r.get('version');
        var document_type_key = r.get('document_type_key');
        var asset_type = r.get('asset_type');
        var cache = r.get('cache');

        this.load(file_id, file_version, file_name, document_type_key, asset_type, cache);
    },

    load: function(file_id, file_version, file_name, document_type_key, asset_type, cache) {
        if (this.file_id != file_id || this.file_version != file_version) {
            this.file_id = file_id;
            this.file_version = file_version;
            this.body.update('');
            this.body.insertFirst(this.getHtml(file_id, file_version, file_name, document_type_key, asset_type, cache));
        }
    },

    getHtml: function(file_id, file_version, file_name, document_type_key, asset_type, cache) {
        switch(asset_type.toUpperCase()) {
            case Phlexible.mediamanager.AUDIO:
                if (cache._mm_preview_audio.substr(0, 2) == 'ok') {
                    return this.createAudioPlayer(256, 256, file_id, file_version, file_name, cache);
                }
                else {
                    return this.createImage(256, 256, file_id, file_version, file_name, cache);
                }
                break;

            case Phlexible.mediamanager.VIDEO:
                if (!cache._mm_preview_video) {//cache._mm_preview_video.substr(0, 2) == 'ok') {
                    return this.createVideoPlayer(256, 256, file_id, file_version, file_name, cache);
                }
                else {
                    return this.createImage(256, 256, file_id, file_version, file_name, cache);
                }
                break;

            case Phlexible.mediamanager.FLASH:
                return this.createFlashPlayer(256, 256, file_id, file_version, file_name, cache);
                break;

            case Phlexible.mediamanager.IMAGE:
            default:
                return this.createImage(256, 256, file_id, file_version, file_name, cache);
                break;
        }
    },

    getLink: function(file_id, template_key, file_version, cache) {
        var parameters = {
            file_id: file_id,
            template_key: template_key
        };
        if (file_version) {
            parameters['file_version'] = file_version;
        }
        if (cache && cache[template_key]) {
            parameters['cache'] = cache[template_key];
        } else if (cache !== false) {
            parameters['waiting'] = 1;
        }

        return Phlexible.Router.generate('mediamanager_media', parameters);
    },

    empty: function() {
        this.body.update('');
        this.body.insertFirst(this.createNoPreview());
    },

    createNoPreview: function() {
        return {
            tag: 'table',
            border: 0,
            width: '100%',
            height: '100%',
            children: [{
                tag: 'tr',
                children: [{
                    tag: 'td',
                    align: 'center',
                    valign: 'middle',
                    html: this.strings.no_preview_available
                }]
            }]
        };
        return '<table border="0" width="100%" height="100%"><tr><td align="center" valign="middle">' + this.strings.no_preview_available + '</td></tr></table>';
    },

    createFlashPlayer: function(width, height, file_id, file_version, file_name, cache) {
        var link = this.getLink(file_id, file_version) + '/' + file_name + '.swf';

        return {
            tag: 'embed',
            src: link,
            width: width,
            height: height,
            allowfullscreen: 'true',
            allowscriptaccess: 'always',
            wmode: 'transparent',
            flashvars: link
        };
        return '<embed src="' + link + '" width="' + width + '" height="' + height + '" allowfullscreen="true" allowscriptaccess="always" wmode="transparent" flashvars="' + link + '" />';
    },

    createAudioPlayer: function(width, height, file_id, file_version, file_name, cache) {
        var image = this.getLink(file_id, '_mm_preview_player', file_version, cache);
        var audio = this.getLink(file_id, '_mm_preview_audio', file_version, false) + '/name/' + file_name + '.mp3';
        //var link = '&file=' + audio + '&image=' + image + '&height=' + height + '&width=' + width + '';

        return {
            tag: 'audio',
            controls: 'controls',
            poster: image,
            children: [{
                tag: 'source',
                src: audio,
                type: 'audio/mpeg'
            }]
        };
        return '<embed src="' + Phlexible.component('/mediamanager/flash/player.swf') + '" width="' + width + '" height="' + height + '" allowfullscreen="true" allowscriptaccess="always" wmode="transparent" flashvars="' + link + '" />';
    },

    createVideoPlayer: function(width, height, file_id, file_version, file_name, cache) {
        var image = this.getLink(file_id, '_mm_preview_player', file_version, cache);
        var video_mp4 = this.getLink(file_id, '_mm_preview_video_mp4', file_version, false) + '/name/' + file_name + '.mp4';
        var video_ogg = this.getLink(file_id, '_mm_preview_video_ogg', file_version, false) + '/name/' + file_name + '.ogv';
        //var link = '&file=' + video + '&image=' + image + '&height=' + height + '&width=' + width + '&overstretch=false';

        return {
            tag: 'video',
            controls: 'controls',
            poster: image,
            width: width,
            height: height,
            children: [{
                tag: 'source',
                src: video_mp4,
                type: 'video/mp4'
            },{
                tag: 'source',
                src: video_ogg,
                type: 'video/ogg'
            }]
        };
        return '<embed src="' + Phlexible.component('/mediamanager/flash/player.swf') + '" width="' + width + '" height="' + height + '" allowfullscreen="true" allowscriptaccess="always" wmode="transparent" flashvars="' + link + '" />';
    },

    createImage: function(width, height, file_id, file_version, file_name, cache) {
        var link = this.getLink(file_id, '_mm_extra', file_version, cache);

        return {
            tag: 'img',
            style: 'border: 1px solid lightgray;',
            alt: file_name,
            src: link,
            width: width,
            height: height
        };
        return '<img style="border: 1px solid lightgray;" alt="' + file_name + '" src="' + link + '" width="' + width + '" height="' + height + '" />';
    },

    createText: function(width, height, file_id, file_name) {

    }
});

Ext.reg('mediamanager-filepreviewpanel', Phlexible.mediamanager.FilePreviewPanel);
