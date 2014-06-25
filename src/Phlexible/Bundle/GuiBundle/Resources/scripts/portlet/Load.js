Ext.namespace('Phlexible.gui.portlet');

Phlexible.gui.portlet.Load = Ext.extend(Ext.ux.Portlet, {
    title: Phlexible.gui.Strings.server_load,
    strings: Phlexible.gui.Strings,
    bodyStyle: 'padding: 5px 5px 5px 5px',
    iconCls: 'p-gui-load-icon',

    COLOR1: '#eacc00',
    COLOR5: '#ea8f00',
    COLOR15: '#b1441e',

    initComponent: function() {
//        if (Phlexible.StartMessage) {
//            Phlexible.StartMessage.on('task', this.processMessage, this);
//        }

        this.tpl = new Ext.XTemplate(
            '<table>',
            '<tr>',
            '<tpl if="!Ext.isIE"><td><div style="background-color: ' + this.COLOR1 + '; width: 10px; height: 8px; border: 1px solid black;" /></td></tpl>',
            '<td>' + this.strings['1_min_avg'] + ':</td>',
            '<td>{[values.l1.toFixed(2)]}</td>',
            '</tr>',
            '<tr>',
            '<tpl if="!Ext.isIE"><td><div style="background-color: ' + this.COLOR5 + '; width: 10px; height: 8px; border: 1px solid black;" /></td></tpl>',
            '<td>' + this.strings['5_min_avg'] + ':</td>',
            '<td>{[values.l5.toFixed(2)]}</td>',
            '</tr>',
            '<tr>',
            '<tpl if="!Ext.isIE"><td><div style="background-color: ' + this.COLOR15 + '; width: 10px; height: 8px; border: 1px solid black;" /></td></tpl>',
            '<td>' + this.strings['15_min_avg'] + ':</td>',
            '<td>{[values.l15.toFixed(2)]}</td>',
            '</tr>',
            '</table>'
        );

        var data = this.record.get('data');
        this.store = new Ext.data.SimpleStore({
            fields: [
                {name: 'l1', type: 'float'},
                {name: 'l5', type: 'float'},
                {name: 'l15', type: 'float'}
            ],
            data: [data]
        });

        this.items = [{
            xtype: 'box',
            hidden: true,
            autoEl: {
                tag: !Ext.isIE ? 'canvas' : 'div',
                style: 'border: 1px solid black;',
                id: 'canvastest',
                height: 100
            },
            listeners: {
                render: {
                    fn: function(c){
                        //console.log('RENDER');
                        //console.log(this.getSize());
                        //console.log(c);
                        //c.setWidth(this.getSize().width - 20);
                    },
                    scope: this
                }
            }
        },{
            html: Phlexible.gui.Strings.waiting_for_data
        }];

        this.on('resize', function(c){
            if (this.getComponent(0).rendered && !Ext.isIE) {
                this.updateCanvas();
            }
        }, this);

        Phlexible.gui.portlet.Load.superclass.initComponent.call(this);
    },

    updateData: function(data){
        var r = new Ext.data.Record({l1: data[0], l5: data[1], l15: data[2]});
        this.store.add(r);

        if (!Ext.isIE) {
            this.updateCanvas();
        }
        this.updateTable(r);
    },

    updateTable: function(r) {
        if (!r) {
            return;
        }

        this.tpl.overwrite(this.getComponent(1).el, r.data);
    },

    updateCanvas: function() {
        var canvas = this.getComponent(0).el.dom;

        if (canvas.getContext){
            this.getComponent(0).show();
            this.getComponent(0).el.dom.setAttribute('width', this.getSize().width - 20);

            canvas.width = canvas.width;

            var range = this.store.getRange();
            var length = range.length;

            var max = 0, drawMax = 0;
            for (var i=0; i < length; i++) {
                if (range[i].data.l1 > max) max = range[i].data.l1;
                if (range[i].data.l5 > max) max = range[i].data.l5;
                if (range[i].data.l15 > max) max = range[i].data.l15;
            }
            drawMax = Math.ceil(max * 1.5);

//            console.log('drawMax: ' + drawMax);
//            console.log('max: ' + max);

            var width = canvas.width;
            //console.log(width);
            var height = canvas.height;

            var mod = height / drawMax;
//            console.log('mod: ' + mod);
            var step = 5;
            var steps = width / step + 1;
            var ctx = canvas.getContext('2d');
            var ri, h, y;
            var startX = 30;
            var endX   = width;
            var startY = 20;
            var endY   = height - 10;

            if (this.store.getCount() >= steps) {
                this.store.remove(this.store.getAt(0));
            }

            ctx.clearRect(0,0,width,height);

            // gradient background
            var lingrad = ctx.createLinearGradient(0, 0, 0, height);
            lingrad.addColorStop(0, '#ddd');
            lingrad.addColorStop(1, '#fff');
            ctx.fillStyle = lingrad;
            ctx.lineWidth = 1;
            ctx.rect(0, 0, width, height);
            ctx.fill();

            ctx.fillStyle = "#444";
            ctx.fillRect(startX - 1, 0, 1, height);

            // gutter
            ctx.lineWidth = 1;
//            console.log('gutter: ' + (height / mod));
            var gutterMod = drawMax / 5;
            for (var i = drawMax - gutterMod; i > gutterMod / 2; i-= gutterMod) {
                y = i * mod;

                ctx.fillStyle = "#bbb";
                ctx.fillRect(startX, y, endX, 1);

                y = height - y + 5;

                ctx.fillStyle = "#222";
                ctx.fillText(i.toFixed(1), 10, y);
            }

            /*
            for (var i = 0; i < length; i++) {
                ri = range[i];

                h = parseInt(ri.data.l1 * mod, 10);
                ctx.fillStyle = "red";
                ctx.fillRect(i * 20 + 0, height - h, 5, h);

                h = parseInt(ri.data.l5 * mod, 10);
                ctx.fillStyle = "orange";
                ctx.fillRect(i * 20 + 5, height - h, 5, h);

                h = parseInt(ri.data.l15 * mod, 10);
                ctx.fillStyle = "yellow";
                ctx.fillRect(i * 20 + 10, height - h, 5, h);
            }
            */

            if (length > 1) {
                h = parseInt(range[0].data.l1 * mod, 10);
                ctx.beginPath();
                ctx.strokeStyle = "#ba0";
                ctx.fillStyle = Phlexible.gui.portlet.COLOR1;
                ctx.lineWidth = 1;
                ctx.moveTo(startX, height);
                ctx.lineTo(startX, height - h);
                for (var i = 1; i < length; i++) {
                    ri = range[i];

                    h = parseInt(ri.data.l1 * mod, 10);
                    ctx.lineTo(startX + i * step, height - h);
                }
                ctx.lineTo(startX + (i - 1) * step, height);
                ctx.lineTo(startX, height);
                ctx.fill();
                ctx.stroke();
                ctx.closePath();

                h = parseInt(range[0].data.l5 * mod, 10);
                ctx.beginPath();
                ctx.lineWidth = 1.5;
                ctx.strokeStyle = Phlexible.gui.portlet.COLOR5;
                ctx.lineWidth = 1.5;
                ctx.moveTo(startX, height - h);
                for (var i = 1; i < length; i++) {
                    ri = range[i];

                    h = parseInt(ri.data.l5 * mod, 10);
                    ctx.lineTo(startX + i * step, height - h);
                }
                ctx.stroke();
                ctx.closePath();

                h = parseInt(range[0].data.l15 * mod, 10);
                ctx.beginPath();
                ctx.strokeStyle = Phlexible.gui.portlet.COLOR15;
                ctx.lineWidth = 1.5;
                ctx.moveTo(startX, height - h);
                for (var i = 1; i < length; i++) {
                    ri = range[i];

                    h = parseInt(ri.data.l15 * mod, 10);
                    ctx.lineTo(startX + i * step, height - h);
                }
                ctx.stroke();
                ctx.closePath();
            }
        }
    }
});
