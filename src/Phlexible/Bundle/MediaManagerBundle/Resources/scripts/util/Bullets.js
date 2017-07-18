Ext.provide('Phlexible.mediamanager.util.Bullets');
Ext.provide('Phlexible.mediamanager.Bullets');

Phlexible.mediamanager.util.Bullets = function () {
    bullets = '';
};
Ext.extend(Phlexible.mediamanager.util.Bullets, Ext.util.Observable, {
    versionsBullet: function (values) {
        if (values.has_versions) {
            return '<img src="' + Phlexible.bundleAsset('/phlexiblemediamanager/images/bullet_versions.gif') + '" width="8" height="12" style="vertical-align: middle;" />';
        }
        return '';
    },
    presentBullet: function (values) {
        if (!values.present) {
            return '<img src="' + Phlexible.bundleAsset('/phlexiblemediamanager/images/bullet_cross.gif') + '" width="8" height="12" style="vertical-align: middle;" />';
        }
        return '';
    },
    usageBullet: function (values) {
        if (!values.used) {
            return '';
        }
        if (values.used & 8) {
            return '<img src="' + Phlexible.bundleAsset('/phlexiblemediamanager/images/bullet_green.gif') + '" width="8" height="12" style="vertical-align: middle;" />';
        } else if (values.used & 4) {
            return '<img src="' + Phlexible.bundleAsset('/phlexiblemediamanager/images/bullet_yellow.gif') + '" width="8" height="12" style="vertical-align: middle;" />';
        } else if (values.used & 2) {
            return '<img src="' + Phlexible.bundleAsset('/phlexiblemediamanager/images/bullet_gray.gif') + '" width="8" height="12" style="vertical-align: middle;" />';
        } else if (values.used & 1) {
            return '<img src="' + Phlexible.bundleAsset('/phlexiblemediamanager/images/bullet_black.gif') + '" width="8" height="12" style="vertical-align: middle;" />';
        }
        return '';
    },
    buildBullets: function (values) {
        var bullets = '';
        bullets += this.versionsBullet(values);
        bullets += this.presentBullet(values);
        bullets += this.usageBullet(values);
        this.bullets = bullets;
    },
    getWithTrailingSpace: function (value) {
        this.buildBullets(value);
        var bullets = this.bullets;
        this.bullets = '';
        if (bullets) {
            bullets += ' ';
        }
        return bullets;
    }
});
