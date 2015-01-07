Ext.namespace('Phlexible.mediamanager');

Phlexible.mediamanager.IMAGE = 'IMAGE';
Phlexible.mediamanager.VIDEO = 'VIDEO';
Phlexible.mediamanager.FLASH = 'FLASH';
Phlexible.mediamanager.AUDIO = 'AUDIO';
Phlexible.mediamanager.DOCUMENT = 'DOCUMENT';
Phlexible.mediamanager.ARCHIVE = 'ARCHIVE';
Phlexible.mediamanager.OTHER = 'OTHER';

Phlexible.mediamanager.Rights = {
    FOLDER_READ: 'FOLDER_READ',
    FOLDER_CREATE: 'FOLDER_CREATE',
    FOLDER_MODIFY: 'FOLDER_MODIFY',
    FOLDER_DELETE: 'FOLDER_DELETE',
    FOLDER_RIGHTS: 'FOLDER_RIGHTS',
    FILE_READ: 'FILE_READ',
    FILE_CREATE: 'FILE_CREATE',
    FILE_MODIFY: 'FILE_MODIFY',
    FILE_DELETE: 'FILE_DELETE',
    FILE_DOWNLOAD: 'FILE_DOWNLOAD'
};

Phlexible.mediamanager.DeletePolicy = {
    DELETE_ALL: 'delete_all',
    DELETE_OLD: 'delete_old',
    HIDE_OLD: 'hide_old'
};
