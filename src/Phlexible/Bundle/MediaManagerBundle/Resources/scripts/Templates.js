Phlexible.mediamanager.templates.StartGroup = new Ext.XTemplate(
    '<div id="{groupId}" class="x-grid-group {cls}">',
    '<div id="{groupId}-hd" class="x-grid-group-hd" style="{style}"><div>{text}</div></div>',
    '<div id="{groupId}-bd" class="x-grid-group-body">'
);

Phlexible.mediamanager.templates.UsedString =
    '{[Phlexible.mediamanager.Bullets.getWithTrailingSpace(values.record.data)]}'
    /*
     +
     '<tpl if="!values.record.data.present">' +
     '<img src="' + Phlexible.component('/mediamanager/images/bullet_cross.gif')+'" width="8" height="12" style="vertical-align: middle;" />' +
     '</tpl>' +
     '<tpl if="values.record.data.used&8">' +
     '<img src="' + Phlexible.component('/mediamanager/images/bullet_green.gif')+'" width="8" height="12" style="vertical-align: middle;" />' +
     '</tpl>' +
     '<tpl if="values.record.data.used&4">' +
     '<img src="' + Phlexible.component('/mediamanager/images/bullet_yellow.gif')+'" width="8" height="12" style="vertical-align: middle;" />' +
     '</tpl>' +
     '<tpl if="values.record.data.used&2">' +
     '<img src="' + Phlexible.component('/mediamanager/images/bullet_gray.gif')+'" width="8" height="12" style="vertical-align: middle;" />' +
     '</tpl>' +
     '<tpl if="values.record.data.used&1">' +
     '<img src="' + Phlexible.component('/mediamanager/images/bullet_black.gif')+'" width="8" height="12" style="vertical-align: middle;" />' +
     '</tpl>' +
     '<tpl if="values.record.data.used"> </tpl>' +
     '<tpl if="values.record.data.focal">' +
     '<img src="' + Phlexible.component('/focalpoint/images/bullet_focal.gif')+'" width="8" height="12" style="vertical-align: middle;" />' +
     ' ' +
     '</tpl>'
     */
;

Phlexible.mediamanager.templates.NameString =
    '<tpl if="values.record.data.hidden"><span style="text-decoration: line-through;"></tpl>' +
        '{values.record.data.name}' +
        '<tpl if="values.record.data.hidden"></span></tpl>';

Phlexible.mediamanager.templates.GridRowExtra = new Ext.XTemplate(
    '<div class="p-item x-grid3-row p-mediamanager-box-default p-mediamanager-extralarge-box">',
    '<div class="p-mediamanager-extralarge-thumbs">',
    '<div class="p-icon-area {[Phlexible.documenttypes.DocumentTypes.getClass(values.record.data.document_type_key)]}">',
    '<table cellspacing="0" align="center">',
    '<tr>',
    '<td>',
    '<img width="256" height="256" src="<tpl if="values.record.data.cache._mm_extra">{[values.record.data.cache._mm_extra]}</tpl><tpl if="!values.record.data.cache._mm_extra">{[Phlexible.Router.generate(\"mediamanager_media\", {file_id: values.record.data.id, template_key: \"_mm_extra\", file_version: values.record.data.version, _dc: new Date().format(\"U\")})]}</tpl>" />',
    '</td>',
    '</tr>',
    '</table>',
    '</div>',
    '<div class="p-text-area">',
    '<div class="p-text x-grid3-cell x-grid3-td-name">',
    '<div class="p-name">' + Phlexible.mediamanager.templates.UsedString + Phlexible.mediamanager.templates.NameString + '</div>',
    '</div>',
    '</div>',
    '</div>',
    '</div>'
);
Phlexible.mediamanager.templates.GridRowLarge = new Ext.XTemplate(
    '<div class="p-item x-grid3-row p-mediamanager-box-default p-mediamanager-large-box">',
    '<div class="p-mediamanager-large-thumbs">',
    '<div class="p-icon-area {[Phlexible.documenttypes.DocumentTypes.getClass(values.record.data.document_type_key)]}">',
    '<table cellspacing="0" align="center">',
    '<tr>',
    '<td>',
    '<img width="96" height="96" src="<tpl if="values.record.data.cache._mm_large">{[values.record.data.cache._mm_large]}</tpl><tpl if="!values.record.data.cache._mm_large">{[Phlexible.Router.generate(\"mediamanager_media\", {file_id: values.record.data.id, template_key: \"_mm_large\", file_version: values.record.data.version, _dc: new Date().format(\"U\")})]}</tpl>" />',
    '</td>',
    '</tr>',
    '</table>',
    '</div>',
    '<div class="p-text-area">',
    '<div class="p-text x-grid3-cell x-grid3-td-name">',
    '<div class="p-name">' + Phlexible.mediamanager.templates.UsedString + Phlexible.mediamanager.templates.NameString + '</div>',
    '</div>',
    '</div>',
    '</div>',
    '</div>'
);
Phlexible.mediamanager.templates.GridRowMedium = new Ext.XTemplate(
    '<div class="p-item x-grid3-row p-mediamanager-box-default p-mediamanager-medium-box">',
    '<div class="p-mediamanager-medium-thumbs">',
    '<div class="p-icon-area {[Phlexible.documenttypes.DocumentTypes.getClass(values.record.data.document_type_key)]}">',
    '<table cellspacing="0" align="center">',
    '<tr>',
    '<td>',
    '<img width="48" height="48" src="<tpl if="values.record.data.cache._mm_medium">{[values.record.data.cache._mm_medium]}</tpl><tpl if="!values.record.data.cache._mm_medium">{[Phlexible.Router.generate(\"mediamanager_media\", {file_id: values.record.data.id, template_key: \"_mm_medium\", file_version: values.record.data.version, _dc: new Date().format(\"U\")})]}</tpl>" />',
    '</td>',
    '</tr>',
    '</table>',
    '</div>',
    '<div class="p-text-area">',
    '<div class="p-text x-grid3-cell x-grid3-td-name">',
    '<div class="p-name">' + Phlexible.mediamanager.templates.UsedString + Phlexible.mediamanager.templates.NameString + '</div>',
    '</div>',
    '</div>',
    '</div>',
    '</div>'
);
Phlexible.mediamanager.templates.GridRowSmall = new Ext.XTemplate(
    '<div class="p-item x-grid3-row p-mediamanager-box-default p-mediamanager-small-box">',
    '<div class="p-mediamanager-small-thumbs">',
    '<div class="p-icon-area {[Phlexible.documenttypes.DocumentTypes.getClass(values.record.data.document_type_key)]}-small">',
    '<table cellspacing="0" align="center">',
    '<tr>',
    '<td>',

    '</td>',
    '</tr>',
    '</table>',
    '</div>',
    '<div class="p-text-area">',
    '<div class="p-text x-grid3-cell x-grid3-td-name">',
    '<div class="p-name">' + Phlexible.mediamanager.templates.UsedString + Phlexible.mediamanager.templates.NameString + '</div>',
    '</div>',
    '</div>',
    '</div>',
    '</div>'
);

Phlexible.mediamanager.templates.GridRowTile = new Ext.XTemplate(
    '<div class="p-item x-grid3-row p-mediamanager-box-default p-mediamanager-tile-box">',
    '<div class="p-mediamanager-tile-thumbs">',
    '<div class="p-icon-area {[Phlexible.documenttypes.DocumentTypes.getClass(values.record.data.document_type_key)]}">',
    '<table cellspacing="0" align="center">',
    '<tr>',
    '<td>',
    '<img width="48" height="48" src="<tpl if="values.record.data.cache._mm_medium">{[values.record.data.cache._mm_medium]}</tpl><tpl if="!values.record.data.cache._mm_medium">{[Phlexible.Router.generate(\"mediamanager_media\", {file_id: values.record.data.id, template_key: \"_mm_medium\", file_version: values.record.data.version, _dc: new Date().format(\"U\")})]}</tpl>" />',
    '</td>',
    '</tr>',
    '</table>',
    '</div>',
    '<div class="p-text-area">',
    '<div class="p-text x-grid3-cell x-grid3-td-name">',
    '<div class="p-name">' + Phlexible.mediamanager.templates.UsedString + Phlexible.mediamanager.templates.NameString + '</div>',
    '<div class="p-mimeType">{values.record.data.document_type}</div>',
    '<div class="p-fileSize">{[Phlexible.Format.size(values.record.data.size)]}</div>',
    '</div>',
    '</div>',
    '</div>',
    '</div>'
);
Phlexible.mediamanager.templates.DragSingle = new Ext.XTemplate(
    '<div class="p-dragsingleThumbnails">',
    '<img class="thumb" src="{[Phlexible.Router.generate(\"mediamanager_media\", {file_id: values[0].data.id, template_key: \"_mm_large\"})]}" />',
    '</div>'
);
Phlexible.mediamanager.templates.DragMulti = new Ext.XTemplate(
    '<div class="p-dragmultiThumbnails">',
    '<tpl for="values">',
    '<img class="thumb" src="{[Phlexible.Router.generate(\"mediamanager_media\", {file_id: values.data.id, template_key: \"_mm_medium\"})]}" />',
    '</tpl>',
    '</div>'
);

