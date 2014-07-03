/**
 * @class Ext.data.ObjectReader
 * @extends Ext.data.DataReader
 * Data reader class to create an Array of {@link Ext.data.Record} objects from an array of objects
 * based on mappings in a provided Ext.data.Record constructor.<br><br>
 * <p>
 * Example code:
 * <pre><code>
 var RecordDef = Ext.data.Record.create([
 {name: 'name', mapping: 'name'},     // "mapping" property not needed if it's the same as "name"
 {name: 'occupation'}                 // This field will use "occupation" as the mapping.
 ]);
 var myReader = new Ext.data.ObjectReader({
   id: "id"                             // The field that provides an ID for the record (optional)
}, RecordDef);
 </code></pre>
 * <p>
 * This would consume data like this:
 * <pre><code>
 [ {id:1, name:'Bill', occupation:'Gardener'}, {id:2, name:'Ben', occupation:'Horticulturalist'} ]
 </code></pre>
 * @cfg {String} id (optional) The field that provides an ID for the record
 * @constructor
 * Create a new ObjectReader
 * @param {Object} meta Metadata configuration options.
 * @param {Mixed} recordType The definition of the data record type to produce.  This can be either a
 * Record subclass created with {@link Ext.data.Record#create}, or an array of objects with which to call
 * Ext.data.Record.create.  See the {@link Ext.data.Record} class for more details.
 */
Ext.data.ObjectReader = function (meta, recordType) {
    meta = meta || {};
    Ext.data.ObjectReader.superclass.constructor.call(this, meta, recordType || meta.fields);
};
Ext.extend(Ext.data.ObjectReader, Ext.data.DataReader, {
    /**
     * This method is only used by a DataProxy which has retrieved data from a remote server.
     * <p>
     * Data should be provided in the following structure:
     * <pre><code>
     {
       objects : [ { }, { }... ], // An array of objects
       totalSize : 1234           // The total number of records (>= objects.length)
     }
     </code></pre>
     * The 'objects' field is required; other fields are optional.
     * @param {Array} response An object containing an array of objects and meta data.
     * @return {Object} records A data block which is used by an {@link Ext.data.Store} as
     * a cache of Ext.data.Records.
     * <p>
     * Note: This implementation is intended as a convienience to simplify writing DataProxy
     * subclasses and to guide implementations into common field names to return result data.
     * If these fields are unnatural for a proxy, it may call readRecords() directly and handle
     * this functionality itself.
     */
    read: function (response) {
        if (undefined == response.objects) {
            throw {message: "ObjectReader.read: Objects not available"};
        }
        var result = this.readRecords(response.objects);
        if (undefined != response.totalSize)
            result.totalRecords = response.totalSize;
        return result;
    },

    /**
     * Create a data block containing Ext.data.Records from an an array of objects.
     * @param {Object} objects An array of objects.
     * @return {Object} records A data block which is used by an {@link Ext.data.Store} as
     * a cache of Ext.data.Records.
     */
    readRecords: function (objects) {
        var records = [];
        var recordType = this.recordType, fields = recordType.prototype.fields;
        var idField = this.meta.id;
        for (var i = 0; i < objects.length; i++) {
            var object = objects[i];
            var values = {};
            for (var j = 0; j < fields.length; j++) {
                var field = fields.items[j];
                var v = object[field.mapping || field.name] || field.defaultValue;
                v = field.convert(v);
                values[field.name] = v;
            }
            var id = idField ? object[idField] : undefined;
            records[records.length] = new recordType(values, id);
        }
        return {
            records: records,
            totalRecords: records.length
        };
    }
});

/**
 * @class Ext.data.ObjectStore
 * @extends Ext.data.Store
 * Small helper class to make creating Stores from Object data easier.
 * @cfg {Number} id The array index of the record id. Leave blank to auto generate ids.
 * @cfg {Array} fields An array of field definition objects, or field name string as specified to {@link Ext.data.Record#create}
 * @cfg {Object} data The multi-dimensional array of data.
 * @constructor
 * @param {Object} config
 */
Ext.data.ObjectStore = function (config) {
    Ext.data.ObjectStore.superclass.constructor.call(this, Ext.apply(config, {
        reader: new Ext.data.ObjectReader({
                id: config.id
            },
            Ext.data.Record.create(config.fields)
        )
    }));
};
Ext.extend(Ext.data.ObjectStore, Ext.data.Store);
