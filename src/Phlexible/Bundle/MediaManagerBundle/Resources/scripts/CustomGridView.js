Phlexible.mediamanager.CustomGridView = function(config) {
    Ext.apply(this, config);

    this.addEvents(
        /**
         * @event modeChange
         * Fires when the View mode is changed
         * @param {string} newMode The new View mode
         */
        "modeChange"
    );

    Ext.grid.GridView.superclass.constructor.call(this);
};
Ext.extend(Phlexible.mediamanager.CustomGridView, Ext.grid.GroupingView, {
    modeOrder: ["tile","large","extralarge","detail"],
    onLoad: Ext.emptyFn,
    cookies: new Ext.state.CookieProvider(),
    stateKey: null,
    defaultMode: "tile",
    startGroup: Phlexible.mediamanager.templates.StartGroup,

    initTemplates: function() {
        Phlexible.mediamanager.CustomGridView.superclass.initTemplates.apply(this, arguments);
        this.detailRowTemplate = this.templates.row;
        this.detailCellTemplate = this.templates.cell;
        this.customRowTemplate = {
            extralarge: Phlexible.mediamanager.templates.GridRowExtra,
            large: Phlexible.mediamanager.templates.GridRowLarge,
            medium: Phlexible.mediamanager.templates.GridRowMedium,
            small: Phlexible.mediamanager.templates.GridRowSmall,
            tile: Phlexible.mediamanager.templates.GridRowTile
            //timeline: Phlexible.mediamanager.templates.GridRowTimeline
        };
        this.customCellTemplate = null;
        this.restoreState();
    },
    renderUI: function() {
        Phlexible.mediamanager.CustomGridView.superclass.renderUI.apply(this, arguments);
    },
    saveState: function() {
        if(this.stateKey){
            this.cookies.set(this.stateKey,this.mode);
        }
    },
    restoreState: function() {
        var mode = this.defaultMode;
        if(this.stateKey){
            var mode = this.cookies.get(this.stateKey) || this.defaultMode;
        }
        this.setViewMode(mode);
    },
    updateAllColumnWidths: function(){
        var tw = this.getTotalWidth();
        var clen = this.cm.getColumnCount();
        var ws = [];
        for(var i = 0; i < clen; i++){
            ws[i] = this.getColumnWidth(i);
        }

        this.innerHd.firstChild.firstChild.style.width = tw;

        for(var i = 0; i < clen; i++){
            var hd = this.getHeaderCell(i);
            hd.style.width = ws[i];
        }

        if(this.templates.cell){
            var ns = this.getRows(), row, trow;
            for(var i = 0, len = ns.length; i < len; i++){
                row = ns[i];
                row.style.width = tw;
                if(row.firstChild){
                    row.firstChild.style.width = tw;
                    trow = row.firstChild.rows[0];
                    for (var j = 0; j < clen; j++) {
                       trow.childNodes[j].style.width = ws[j];
                    }
                }
            }
        }

        this.onAllColumnWidthsUpdated(ws, tw);
    },
    updateColumnWidth: function(col, width){
        var w = this.getColumnWidth(col);
        var tw = this.getTotalWidth();

        this.innerHd.firstChild.firstChild.style.width = tw;
        var hd = this.getHeaderCell(col);
        hd.style.width = w;

        if (this.templates.cell) {
            var ns = this.getRows();
            for (var i = 0, len = ns.length; i < len; i++) {
                ns[i].style.width = tw;
                ns[i].firstChild.style.width = tw;
                ns[i].firstChild.rows[0].childNodes[col].style.width = w;
            }
        }

        this.onColumnWidthUpdated(col, w, tw);
    },
    detailView: function(){
        this.setViewMode("detail");
    },
    extraLargeThumbnails: function(){
        this.setViewMode("extralarge");
    },
    largeThumbnails: function(){
        this.setViewMode("large");
    },
    mediumThumbnails: function(){
        this.setViewMode("medium");
    },
    smallThumbnails: function(){
        this.setViewMode("small");
    },
    tileView: function(){
        this.setViewMode("tile");
    },
    timelineView: function(){
        this.setViewMode("timeline");
    },
    nextViewMode: function(){
        var curMode = this.mode || "tile";
        var modeOrder = this.modeOrder;
        var curIndex = modeOrder.indexOf(curMode);
        curIndex++;
        if(curIndex>=modeOrder.length){
            curIndex = 0;
        }
        this.setViewMode(modeOrder[curIndex]);
    },
    setViewMode: function(newMode){
        this.mode = newMode;
        if(newMode=="detail"){
            this.templates.row = this.detailRowTemplate;
            this.templates.cell = this.detailCellTemplate;
        } else {
            this.templates.row = this.customRowTemplate[newMode];
            //this.customRowTemplate.setMode(newMode);
            this.templates.cell = this.customCellTemplate;
        }
        if(this.el){
            this.refresh();
        }
        this.saveState();

        this.fireEvent("modeChange", this, this.mode);
    },
    customDoRender: function(cs,rs,ds,startRow,colCount,stripe){
        var cellTpl = this.templates.cell, rowTpl = this.templates.row, maxColValue = colCount-1;
        var width = "width:" + this.getTotalWidth() + ";";
        var buf = [], cellBuf, c, cell = {}, row = {tstyle:width}, r;
        for(var i=0, len=rs.length; i<len; i++){
            r = rs[i];
            cellBuf = [];
            var rowIndex = (i+startRow);
            if(cellTpl){
                for(var j=0; j<colCount; j++){
                    c = cs[j];
                    cell.id = c.id;
                    cell.css = j===0 ? "x-grid3-cell-first " : (j==maxColValue ? "x-grid3-cell-last " : "");
                    cell.attr = cell.cellAttr = "";
                    cell.value = c.renderer(r.data[c.name],cell,r,rowIndex,j,ds);
                    cell.record = r;
                    cell.style = c.style;
                    if(cell.value==undefined || cell.value===""){
                        cell.value = " ";
                    }
                    if(r.dirty && typeof r.modified[c.name]!=="undefined"){
                        cell.css += " x-grid3-dirty-cell";
                    }
                    cellBuf[cellBuf.length] = cellTpl.apply(cell);
                }
            }
            var altBuf = [];
            if(stripe && ((rowIndex+1)%2===0)){
                altBuf[0] = "x-grid3-row-alt";
            }
            if(r.dirty){
                altBuf[1] = "x-grid3-dirty-row";
            }
            row.record = r;
            if(this.getRowClass){
                altBuf[2] = this.getRowClass(r,rowIndex,row,ds);
            }
            row.alt = altBuf.join(" ");
            row.cells = cellBuf.join("");
            buf[buf.length] = rowTpl.apply(row);
        }
        buf[buf.length] = "<div style='clear: both;'></div>";


        return buf.join("");
    },
    doRender: function(cs,rs,ds,startRow,colCount,stripe){
        if(rs.length<1){
            return "";
        }
        var groupField = this.getGroupField();
        var colIndex = this.cm.findColumnIndex(groupField);

        this.enableGrouping = !!groupField;

        if(!this.enableGrouping || this.isUpdating){
            return this.customDoRender.apply(this, arguments);
        }
        var gstyle = "width:" + this.getTotalWidth() + ";";

        var gidPrefix = this.grid.getGridEl().id;
        var cfg = this.cm.config[colIndex];
        var groupRenderer = cfg.groupRenderer || cfg.renderer;
        var prefix = this.showGroupName ? (cfg.groupName || cfg.header) + ": " : "";
        var groups = [],curGroup,i,len,gid;
        for(i=0, len=rs.length; i<len; i++){
            var rowIndex = startRow + i;
            var r = rs[i], gvalue = r.data[groupField], g = this.getGroup(gvalue,r,groupRenderer,rowIndex,colIndex,ds);
            if(!curGroup || curGroup.group!=g){
                gid = gidPrefix + "-gp-" + groupField + "-" + g;
                var isCollapsed  = typeof this.state[gid] !== 'undefined' ? !this.state[gid] : this.startCollapsed;
                var gcls = isCollapsed ? 'x-grid-group-collapsed' : '';
                curGroup = {
                    group: g,
                    gvalue: gvalue,
                    text: prefix + g,
                    groupId: gid,
                    startRow: rowIndex,
                    rs: [r],
                    cls: gcls,
                    style: gstyle
                };
                groups.push(curGroup);
            } else {
                curGroup.rs.push(r);
            }
            r._groupId = gid;
        }
        var buf = [];
        for(i = 0; i<groups.length; i++){
            var g = groups[i];
            this.doGroupStart(buf,g,cs,ds,colCount);
            buf[buf.length] = this.customDoRender.call(this,cs,g.rs,ds,g.startRow,colCount,stripe);
            this.doGroupEnd(buf,g,cs,ds,colCount);
        }
        return buf.join("");
    }
});
