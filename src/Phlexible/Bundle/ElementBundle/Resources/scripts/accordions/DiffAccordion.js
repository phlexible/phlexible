Phlexible.elements.accordion.Diff = Ext.extend(Ext.Panel, {
    title: Phlexible.elements.Strings.differences,
    cls: 'p-elements-diff-accordion',
    iconCls: 'p-element-diff-icon',
    border: false,
    height: 200,
    autoHeight: true,
    bodyStyle: 'padding: 5px;',

    key: 'comment',

    initComponent: function () {
        this.element.on({
            diff: {
                fn: function (diff, cb, scope) {
                    if (cb && scope) {
                        cb.call(scope, diff, this);
                        this.expand();
                    }
                    else if (cb) {
                        cb(diff, this);
                        this.expand();
                    }
                    else {
                        this.empty();
                    }
                },
                scope: this
            },
            clearDiff: {
                fn: function () {
                    this.empty();
                },
                scope: this
            }
        });

        this.html = Phlexible.elements.Strings.no_differences;

        Phlexible.elements.accordion.Diff.superclass.initComponent.call(this);
    },

    empty: function () {
        this.body.update(Phlexible.elements.Strings.no_differences);
    },

    load: function (data, element) {
        this.empty();

        this.diff = element.data.diff;
    }
});

Ext.reg('elements-diffaccordion', Phlexible.elements.accordion.Diff);
