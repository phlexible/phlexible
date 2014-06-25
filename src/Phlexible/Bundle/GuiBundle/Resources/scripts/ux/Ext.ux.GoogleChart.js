/*jsl:ignoreall*/
/**
 * Ext.ux.GoogleChart Extension Class for Ext 2.x Library
 *
 * @author Cumpa
 * @version 1.0.2
 * @class Ext.ux.GoogleChart
 * @extends Ext.BoxComponent
 * This is a wrapper class for Google Chart API. 
 * More info at http://code.google.com/apis/chart/
 * @license Ext.ux.GoogleChart is licensed under the terms of
 * the Open Source LGPL 3.0 license.  Commercial use is permitted to the extent
 * that the code/component(s) do NOT become part of another Open Source or Commercially
 * licensed development library or toolkit without explicit permission.
 *
 * License details: 
 * http://code.google.com/apis/chart/terms.html
 * http://www.gnu.org/licenses/lgpl.html
 */

Ext.ux.GoogleChart = Ext.extend(Ext.BoxComponent, {
  /**
   * @cfg barSize
   * Bar chart size. See "chbh" parameter in bottom link.
   * http://code.google.com/apis/chart/#bar_charts
   * @type Array
   */
  barSize: null
  /**
   * @cfg barZeroLine
   * Bar chart zero line.
   * http://code.google.com/apis/chart/#chp
   * @type Array
   */
  ,barZeroLine: null
  /**
   * @cfg brush
   * Shortcut for "Solid fill", "Linear gradient", "Linear stripes".
   * For more info see Google documentation.
   * @type Array
   */
  ,brush: null
  /**
   * @cfg chartColors
   * Chart colors.
   * http://code.google.com/apis/chart/#line_bar_pie_colors
   * @type Array
   */
  ,chartColors: null
  /**
   * @cfg {String} chartType Chart type:<ul>
   * <li>line/linexy/linespark: Line charts (http://code.google.com/apis/chart/#line_charts)</li>
   * <li>pie/pie3d: Pie charts (http://code.google.com/apis/chart/#pie_charts)</li>
   * <li>barhorizontal/barvertical/bargrouphorizontal/bargroupvertical: Bar charts (http://code.google.com/apis/chart/#bar_charts)</li>
   * <li>venn: Venn diagrams (http://code.google.com/apis/chart/#venn)</li>
   * <li>scatter: Scatter plots (http://code.google.com/apis/chart/#scatter_plot)</li>
   * <li>radar/radars: Radar charts (http://code.google.com/apis/chart/#radar)</li>
   * <li>maps: Maps (http://code.google.com/apis/chart/#maps)</li>
   * <li>gmeter: Google-o-meter (http://code.google.com/apis/chart/#gom)</li>
   * </ul>
   */
  ,chartType: "pie"
  /**
   * @cfg dataScaling
   * Text encoding with data scaling.
   * http://code.google.com/apis/chart/#data_scaling
   * @type Array
   */
  ,dataScaling: null
  /**
   * @cfg {String} dataType Type of data send to Google:<ul>
   * <li>text: Text enconding (http://code.google.com/apis/chart/#text)</li>
   * <li>simple: Simple enconding (http://code.google.com/apis/chart/#simple)</li>
   * <li>extended: Extended enconding (extended)</li>
   * </ul>
   */
  ,dataType: "text"
  /**
   * @cfg gridLines
   * Grid lines.
   * http://code.google.com/apis/chart/#grid
   * @type Array
   */
  ,gridLines: null
  /**
   * @cfg height
   * Height of chart in pixels (defaults to 100).
   * @type Number
   */
  ,height: 100
  /**
   * @cfg labels
   * Pie chart and Google-o-meter labels.
   * http://code.google.com/apis/chart/#pie_labels
   * @type Array
   */
  ,labels: null
  /**
   * @cfg legend
   * Specify a legend array.
   * http://code.google.com/apis/chart/#chdl
   * @type Array
   */
  ,legend: null
  /**
   * @cfg  mapArea
   * Geographical area. See "chtm" parameter in bottom link.
   * http://code.google.com/apis/chart/#maps
   * @type String
   */
  ,mapArea: null
  /**
   * @cfg  mapCodes
   * Codes for country. See "chld" parameter in bottom link.
   * http://code.google.com/apis/chart/#maps
   * @type Array
   */
  ,mapCodes: null
  /**
   * @cfg store
   * Ext.data.Store for reading data.
   * Don't use "autoLoad" config property for Ext.data.Store
   * @type Ext.data.Store
   */
  ,store: null
  /**
   * @cfg title
   * Chart's title.
   * @type String
   */
  ,title: null
  /**
   * @cfg url
   * Url for Google Chart API service (default http://chart.apis.google.com/chart?).
   * @type String
   */
  ,url: "http://chart.apis.google.com/chart?"
  /**
   * @cfg width
   * Width of chart in pixels (defaults to 250).
   * http://code.google.com/apis/chart/#chtt
   * @type Number
   */
  ,width: 250
  /**
   * Add a Multiple axis label
   * http://code.google.com/apis/chart/#multiple_axes_labels
   * @param {String} key The key to associate with the item.
   * @param {Object} object The axis object:<ul>
   * <li><b>type:</b>{String} x|t|y|r</li>
   * <li><b>labels:</b>{Array}</li>
   * <li><b>labelsposition</b>{Array}</li>
   * <li><b>startrange</b>{Number}</li>
   * <li><b>endrange</b>{Number}</li>
   * <li><b>color</b>{String}</li>
   * <li><b>fontsize</b>{Number}</li>
   * <li><b>alignment</b>{Number}</li>
   *</ul>
   */
  , addAxis: function(key, object) {
    if (!this.axies) {
      this.axies = new Ext.util.MixedCollection();
    }
    this.axies.add(key, object);
  }
  /**
   * Add a chart data
   * @param {String} key The key to associate with the item.
   * @param {Array} object Array data.
   */
  , addData: function(key, object) {
    if (!this.data) {
      this.data = new Ext.util.MixedCollection();
    }
    this.data.add(key, object);
  }
  /**
   * Add a fill data
   * @param {String} key The key to associate with the item.
   * @param {Array} object See google documentation for data.
   */
  , addFillArea: function(key, object) {
    if (!this.fillArea) {
      this.fillArea = new Ext.util.MixedCollection();
    }
    this.fillArea.add(key, object);
  }
  /**
   * Add a Line style
   * @param {String} key The key to associate with the item.
   * @param {Array} object See google documentation for data.
   * http://code.google.com/apis/chart/#barchartlines
   */
  , addLineStylePriority: function(key, object) {
    if (!this.lineStylePriority) {
      this.lineStylePriority = new Ext.util.MixedCollection();
    }
    this.lineStylePriority.add(key, object);
  }
  /**
   * Add a Line style
   * @param {String} key The key to associate with the item.
   * @param {Array} object See google documentation for data.
   * http://code.google.com/apis/chart/#linechartlines
   */
  , addLineStyle: function(key, object) {
    if (!this.lineStyle) {
      this.lineStyle = new Ext.util.MixedCollection();
    }
    this.lineStyle.add(key, object);
  }
  /**
   * Add a Marker.
   * You can also use this method for all "objects" require "chm" parameter
   * @param {String} key The key to associate with the item.
   * @param {Array} object See google documentation for data.
   * http://code.google.com/apis/chart/#shape_markers
   */
  , addMarker: function(key, object) {
    if (!this.marker) {
      this.marker = new Ext.util.MixedCollection();
    }
    this.marker.add(key, object);
  }
  /**
   * @return {String} Return the formatted url for chart.
   */
  , getUrl: function(){
      var required = String.format('{0}chs={1}&cht={2}{3}', this.url, this.getSize(), this.getChartType(), this.getChartData());
      return required.concat(this.getOptional());
  }
  /**
   * Repaint chart
   */
  , repaint: function() {
    if (this.el) {
      this.el.set({src: this.getUrl()});
    }
  }
  /**
   * Private
   */
  ,initComponent: function(){
    Ext.ux.GoogleChart.superclass.initComponent.call(this);

    if (this.store) {
      this.storeLoaded = false;

      this.on('beforerender', function(){
        return this.storeLoaded;
      }, this);

      this.store.on('load', function(store, records){
        Ext.each(records, function(rec){
          this.addData(rec.data.id, rec.data.chartdata);
        }, this);
        this.storeLoaded = true;
        this.render(this.renderElement, this.renderPosition);
      }, this);

      this.store.load();
    }
  }
  /**
   * Private
   */
  ,render: function(ct, position){
    this.renderElement = ct;
    this.renderPosition = position;
    Ext.ux.GoogleChart.superclass.render.call(this, ct, position);
  }
  /**
   * Private
   */
  ,onRender: function(ct, position){
    if(!this.el){
        this.el = document.createElement('img');
        this.el.id = this.getId();
        this.el.src = this.getUrl();
    }

    Ext.ux.GoogleChart.superclass.onRender.call(this, ct, position);
  }
  /**
   * Private
   */
  , dataTypes: {
    "text":"t:",
    "simple":"s:",
    "extended":"e:"
  }
  /**
   * Private
   */
  , chartTypes: {
    "line":"lc",
    "linexy":"lxy",
    "linespark":"ls",
    "pie":"p",
    "pie3d":"p3",
    "barhorizontal":"bhs",
    "barvertical":"bvs",
    "bargrouphorizontal":"bhg",
    "bargroupvertical":"bvg",
    "venn":"v",
    "scatter":"s",
    "radar":"r",
    "radars":"rs",
    "maps":"t",
    "gmeter":"gom"
  }
  /**
   * Private
   */
  , getSize: function(){
      return String.format('{0}x{1}', this.width, this.height);
  }
  /**
   * Private
   */
  , getChartType: function(){
      return this.chartTypes[this.chartType];
  }
  /**
   * Private
   */
  , getChartData: function(){
      if (this.dataType == "text") {
        return this.getFormattedValue({
          value: this.data
          ,name: '&chd'
          ,isMixed:true
          ,initvalue:this.dataTypes[this.dataType]
        });
      } else if (this.dataType == "simple") {
        return this.getFormattedValue({
          value: this.data
          ,isMixed:true
          ,name: '&chd'
          ,mixedJoinChar:','
          ,initvalue:this.dataTypes[this.dataType]
          ,fn:function(item){
            return this.simpleEncode(item, this.maxValue || 100);
          }
          ,fnscope: this
          ,defaultvalue:'_'
        });
      } else {
        return this.getFormattedValue({
          value: this.data
          ,isMixed:true
          ,name: '&chd'
          ,mixedJoinChar:','
          ,initvalue:this.dataTypes[this.dataType]
          ,fn:function(item){
            return this.extendedEncode(item);
          }
          ,fnscope: this
          ,defaultvalue:'__'
        });
      }
  }
  /**
   * Private
   */
  , getMarkers: function(){
    if (this.marker || this.fillArea || this.lineStylePriority) {
      var s = '&chm=';
      var p = '';

      if (this.marker) {
        if (s != '&chm=') {
          s = s.concat('|');
        }

        p = '|';

        this.marker.each(function(item, index, length){
          if (index == (length-1)) {
            p = '';
          }
          if (Ext.isArray(item)) {
            s = s.concat(item.join(','), p);
          }
        });
      }

      if (this.fillArea) {
        if (s != '&chm=') {
          s = s.concat('|');
        }
        p = '|';

        this.fillArea.each(function(item, index, length){
          if (index == (length-1)) {
            p = '';
          }
          if (Ext.isArray(item)) {
            if (length = 1) {
              s = s.concat('B,',item.join(','), p);
            } else {
              s = s.concat('b,',item.join(','), p);
            }
          }
        });
      }

      if (this.lineStylePriority) {
        if (s != '&chm=') {
          s = s.concat('|');
        }
        p = '|';

        this.lineStylePriority.each(function(item, index, length){
          if (index == (length-1)) {
            p = '';
          }
          if (Ext.isArray(item)) {
            s = s.concat('D,',item.join(','), p);
          }
        });
      }

      return s;
    } else {
      return '';
    }
  }
  /**
   * Private
   */
  , getFormattedValue: function(config) {
      var s = '';
      if (config) {
        if (config.value && config.name) {
          if (!config.isMixed) {
            var fn = Ext.isArray(config.value) ? function(item){ return item.join(config.joinChar ? config.joinChar : ',')} : function(item) {return item;};
            s = config.name.concat('=', config.initvalue || '', fn(config.value));
          } else {
            var p = config.mixedJoinChar ? config.mixedJoinChar : '|';

            s = config.name.concat('=', config.initvalue || '');

            config.value.each(function(item, index, length){
              if (index == (length-1)) {
                p = '';
              }
              var fn = Ext.isArray(config.value) ? function(item){ return item.join(config.joinChar ? config.joinChar : ',')} : (!config.fn ? function(item) {return item;} : config.fn);
              s = s.concat(fn.call(config.fnscope ||item, item), p);
            });
          }
        } else {
          return config.defaultvalue ? config.name.concat('=', config.initvalue || '', config.defaultvalue) : '';
        }
      }

      return s;
  }
  /**
   * Private
   */
  , getAxies: function(){
    var s = '';
    var sType = '&chxt=';
    var sLabels = '&chxl=';
    var sPositions = '&chxp=';
    var sRange = '&chxr=';
    var sStyle = '&chxs=';
    var p = '|';
    var c = ',';

    if (this.axies) {
      this.axies.each(function(item, index, length){
        if (index == (length-1)) {
          p = '';
          c = '';
        }
        if (item.type) {
          sType = sType.concat(item.type, c);
        }
        if (item.labels) {
          if (Ext.isArray(item.labels)) {
            sLabels = sLabels.concat(String.format('{0}:|{1}{2}',index, item.labels.join('|'), p));
          }
        }
        if (item.labelsposition) {
          if (Ext.isArray(item.labelsposition)) {
            sPositions = sPositions.concat(String.format('{0},{1}{2}',index, item.labelsposition.join(), p));
          }
        }
        if (item.startrange>=0 && item.endrange>=0) {
          sRange = sRange.concat(String.format('{0},{1},{2}',index, item.startrange, item.endrange, p));
        }
        if (item.color) {
          sStyle = sStyle.concat(String.format('{0},{1}',index, item.color));

          if (item.fontsize) {
            sStyle = sStyle.concat(',', item.fontsize);
          }
          if (item.alignment) {
            sStyle = sStyle.concat(',', item.alignment);
          }

          sStyle = sStyle.concat(p);
        }
      });
    }
    if (sType != '&chxt=') {
      s = s.concat(sType);
    }
    if (sLabels != '&chxl=') {
      s = s.concat(sLabels);
    }
    if (sPositions != '&chxp=') {
      s = s.concat(sPositions);
    }
    if (sRange != '&chxr=') {
      s = s.concat(sRange);
    }
    if (sStyle != '&chxs=') {
      s = s.concat(sStyle);
    }
    return s;
  }
  /**
   * Private
   */
  , getOptional: function(){
    var r = '';
    return r.concat(
      this.getFormattedValue({value: this.dataScaling, name: '&chds'}),
      this.getFormattedValue({value: this.labels, name: '&chl', joinChar:'|'}),
      this.getFormattedValue({value: this.chartColors, name: '&chco'}),
      this.getMarkers(),
      this.getFormattedValue({value: this.barSize, name: '&chbh'}),
      this.getFormattedValue({value: this.lineStyle, name: '&chls', isMixed:true}),
      this.getAxies(),
      this.getFormattedValue({value: this.gridLines, name: '&chg'}),
      this.getFormattedValue({value: this.mapArea, name: '&chtm'}),
      this.getFormattedValue({value: this.mapCodes, name: '&chld'}),
      this.getFormattedValue({value: this.brush, name: '&chf'}),
      this.getFormattedValue({value: this.barZeroLine, name: '&chp'}),
      this.getFormattedValue({value: this.legend, name: '&chdl', joinChar:'|'}),
      this.getFormattedValue({value: this.title, name: '&chtt'})
    );
  }
  /**
   * Private
   */
  , extendedEncode: function(valueArray){
      var simpleEncoding = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-.';
      var chartData = [''];
      var esln = simpleEncoding.length;
      var firstChar, secondChar;
      for (var i = 0; i < valueArray.length; i++) {
        var currentValue = valueArray[i];
        if (!isNaN(currentValue) && currentValue >= 0) {
          firstChar = simpleEncoding.charAt(Math.floor(currentValue/esln));
          secondChar = simpleEncoding.charAt((currentValue % esln));
          chartData.push(firstChar+secondChar);
        } else {
          chartData.push('__');
        }
      }

      return chartData.join('');
  }
  /**
   * Private
   */
  , simpleEncode: function(valueArray,maxValue){
    var simpleEncoding = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    var chartData = [''];

    for (var i = 0; i < valueArray.length; i++) {
      var currentValue = valueArray[i];
      if (!isNaN(currentValue) && currentValue >= 0) {
      chartData.push(simpleEncoding.charAt(Math.round((simpleEncoding.length-1) * currentValue / maxValue)));
      }
        else {
        chartData.push('_');
        }
    }
    return chartData.join('');
  }
});

Ext.reg('googlechart', Ext.ux.GoogleChart);
