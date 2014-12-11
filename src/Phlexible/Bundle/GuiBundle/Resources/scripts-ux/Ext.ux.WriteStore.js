/*jsl:ignoreall*/
/*
 * Copyright © Atomic Inc 2007-2008
 * http://jsorm.com
 *
 * This file contains work that is copyrighted and is distributed under one of several licenses.
 * You may not use, modify or distribute this work, except under an approved license.
 * Please visit the Web site listed above to obtain the original work and a license.
 */

// extend the HttpProxy to write
Ext.ux.HttpWriteProxy = Ext.extend(Ext.data.HttpProxy,
    {
        constructor: function (conn) {
            this.superclass = Ext.ux.HttpWriteProxy.superclass;

            // must add events before calling superclass constructor
            this.addEvents(
                'beforeupdate',
                'updateexception',
                'update'
            );

            this.superclass.constructor.call(this, conn);

        },
        update: function (params, callback, scope, arg) {
            if (this.fireEvent("beforeupdate", this, params) !== false) {
                var o = {
                    params: params || {},
                    request: {
                        callback: callback,
                        scope: scope,
                        arg: arg
                    },
                    callback: this.updateResponse,
                    method: 'POST',
                    scope: this
                };
                if (this.useAjax) {
                    Ext.applyIf(o, this.conn);
                    if (this.activeRequest) {
                        Ext.Ajax.abort(this.activeRequest);
                    }
                    this.activeRequest = Ext.Ajax.request(o);
                } else {
                    this.conn.request(o);
                }
            } else {
                callback.call(scope || this, arg, false, null);
            }
        },

        updateResponse: function (o, success, response) {
            delete this.activeRequest;
            if (!success) {
                this.fireEvent("updateexception", this, o, response);
                o.request.callback.call(o.request.scope, o.request.arg, false, response);
                return;
            }
            this.fireEvent("update", this, o, o.request.arg);
            o.request.callback.call(o.request.scope, o.request.arg, true, response);
        }
    });

// Json data writer extension to Reader
Ext.ux.JsonWriterReader = Ext.extend(Ext.data.JsonReader, {
    constructor: function (meta, recordType) {
        this.superclass = Ext.ux.JsonWriterReader.superclass;
        // make sure meta is not blank
        meta = meta || {};
        // call the superclass constructor
        this.superclass.constructor.call(this, meta, recordType || meta.fields);
    },
    // write - input objects, write out JSON
    write: function (records) {
        // hold our new structure
        var obj = {};
        // need to do this way rather than literal, because the property name is a variable
        obj[this.meta.root] = records;
        //if (this.serverMeta) {
        //    obj['metaData'] = this.serverMeta;
        //}
        var j = Ext.util.JSON.encode(obj);
        if (!j) {
            throw{message: "JsonWriter.write: unable to encode records into Json"};
        }
        return(j);
    },
    // we need to override readRecords to ensure we hold onto metaData
    readRecords: function (o) {
        if (o.metaData) {
            this.serverMeta = o.metaData;
        } else {
            delete this.serverMeta;
        }
        return(this.superclass.readRecords.call(this, o));
    }

});
Ext.ux.ObjectReader = Ext.extend(Ext.data.DataReader, {
    constructor: function (meta, recordType) {
        this.superclass = Ext.ux.ObjectReader.superclass;
        meta = meta || {};
        this.superclass.constructor.call(this, meta, recordType || meta.fields);
    },
    read: function (response) {
        var o = response;
        if (!o) {
            throw {message: "ObjectReader.read: object not found"};
        }
        if (o.metaData) {
            delete this.ef;
            this.meta = o.metaData;
            this.recordType = Ext.data.Record.create(o.metaData.fields);
            this.onMetaChange(this.meta, this.recordType, o);
        }
        return this.readRecords(o);
    },

    // private function a store will implement
    onMetaChange: function (meta, recordType, o) {

    },

    simpleAccess: function (obj, subsc) {
        return obj[subsc];
    },

    clone: function (o) {
        // nothing with null
        if (o == null) return o;

        var n = null;
        // depends on type
        var t = typeof(o);
        if (o instanceof Array) {
            n = new Array();
            for (var i = 0; i < o.length; i++)
                n[i] = this.clone(o[i]);
        } else if (o instanceof Object) {
            var n = new Object();
            for (var i in o) {
                n[i] = this.clone(o[i]);
            }
        } else {
            n = o;
        }
        return n;
    },

    readRecords: function (o) {
        o = this.clone(o);
        this.objectData = o;
        var s = this.meta, Record = this.recordType,
            f = Record.prototype.fields, fi = f.items, fl = f.length;

        var getId = function () {
            return(null)
        };
        if (this.meta.id) {
            var idField = this.meta.id;
            getId = function (r) {
                return(rec[idField]);
            }
        }

        var records = [];
        for (var i = 0; i < o.length; i++) {
            var record = new Record(o[i], getId(o[i]));
            records[i] = record;
        }
        return {
            success: true,
            records: records,
            totalRecords: records.length
        };
    },

    // write - input objects, write out cloned objects
    write: function (records) {
        // clone so we do not confuse objects
        return(this.clone(records));
    }
});

/**
 * @class Ext.ux.StoreProxy
 * @extends Ext.data.DataProxy
 * An implementation of Ext.data.DataProxy that gets data from an existing store
 * @constructor
 * @param store the store which contains the data we want to load as an element of
 *   one of the Records in its store.
 * @param field the name of the field within the record
 */
Ext.ux.StoreProxy = Ext.extend(Ext.data.DataProxy, {
    constructor: function (config) {
        this.superclass = Ext.ux.StoreProxy.superclass;
        this.superclass.constructor.call(this);
        config = config || {};
        this.sourceStore = config.store;
        this.sourceField = config.field;

        this.addEvents({
            'beforeupdate': true,
            'updateexception': true,
            'update': true
        });
    },
    /**
     * Load data from the requested source (in this case another store), read the data object into
     * a block of Ext.data.Records directly, and
     * process that block using the passed callback.
     * @param {Object} params The only parameter looked at is ID, which is the ID of the record in the source store
     * @param {Ext.data.DataReader) reader This parameter is not used by the StoreProxy class.
     * @param {Function} callback The function into which to pass the block of Ext.data.records.
     * The function must be passed <ul>
     * <li>The Record block object</li>
     * <li>The "arg" argument from the load function</li>
     * <li>A boolean success indicator</li>
     * </ul>
     * @param {Object} scope The scope in which to call the callback
     * @param {Object} arg An optional argument which is passed to the callback as its second parameter.
     */
    load: function (params, reader, callback, scope, arg) {
        params = params || {};
        var result;
        try {
            var record = this.sourceStore.getById(params.id);
            if (record != null && record != undefined) {
                this.loadId = params.id;
                var data = record.get(this.sourceField);
                result = reader.readRecords(data);
            }
        } catch (e) {
            this.loadId = null;
            this.fireEvent("loadexception", this, arg, null, e);
            callback.call(scope, null, arg, false);
            return;
        }
        callback.call(scope, result, arg, true);
    },
    reconfig: function (config) {
        this.sourceStore = config.store;
        this.sourceField = config.field;
    },
    update: function (params, callback, scope, arg) {
        if (this.fireEvent("beforeupdate", this, params) !== false) {
            try {
                var record = this.sourceStore.getById(this.loadId);
                if (record != null && record != undefined) {
                    // make sure that toString() returns unique
                    var s = Ext.util.JSON.encode(params.data);
                    params.data.toString = function () {
                        return(s)
                    }
                    record.set(this.sourceField, params.data);
                } else {
                    throw "No record matches id " + this.loadId;
                }
            } catch (e) {
                this.fireEvent("updateexception", this, arg, null, e);
                // callback expects arg to be response
                callback.call(scope, params, null, {responseText: e.message});
                return;
            }
            this.fireEvent("update", this, arg);
            callback.call(scope, params, true, {responseText: 'SUCCESS'});
        } else {
            callback.call(scope || this, true, params, {responseText: 'beforeevent prevented update'});
        }
    }
});
/**
 * @author adeitcher
 */
/*
 * Special Record. This differs from the standard ExtJS 2.x Record only in that it has a detailed journal
 * and can do a step-by-step reject.
 */
Ext.ux.WriteRecord = Ext.extend(Ext.data.Record, {
    constructor: function (config) {
        this.superclass = Ext.ux.WriteRecord.superclass;
        Ext.apply(config, this);
        return(config);
    },
    _type: 'Ext.ux.WriteRecord',
    journal: [],
    tmpEdits: 0,
    modCount: {},
    /**
     * Get edits since the last afterEdit() event is sent. Each call to this gets the changes and then clears
     * them. It is almost a short-lived mini-journal for this Record.
     */
    getEdits: function () {
        // only send the edits since the last edit began, not since the last transaction
        var e = this.journal.slice(-this.tmpEdits);
        this.tmpEdits = 0;
        return(e);
    },
    /**
     * Set the named field to the specified value.
     * @param {String} name The name of the field to set.
     * @param {Object} value The value to set the field to.
     * @param {Boolean} silent whether or not this change should be considered by the holding WriteStore
     */
    set: function (name, value, silent) {
        if (!silent) {
            this.journal.push({name: name, old: this.get(name), 'new': value});
            this.tmpEdits++;
        }
        // we need to count how many mods to a particular field are still outstanding
        if (!this.modCount[name])
            this.modCount[name] = 0;
        this.modCount[name]++;

        // do work at the superclass level
        this.superclass.set.call(this, name, value);
    },

    /**
     * We reject changes on a count basis or all of them
     */
    reject: function (silent, count) {
        if (!count || count == 0 || count >= this.journal.length) {
            this.journal.splice(0);
            this.modCount = {};
            this.superclass.reject.call(this, silent);
        } else {
            var e = this.journal.splice(-count).reverse();
            for (var i = 0; i < e.length; i++) {
                var n = e[i].name;
                this.data[n] = e[i].old;
                this.modCount[n]--;
                if (this.modCount[n] <= 0)
                    delete this.modified[e[i].name];
            }
        }
    }
});
/**
 * @author adeitcher
 */
/*
 * This is an extension to Ext.data.Store that adds writing capability. It does so
 * by Ext.apply(Ext.data.Store.prototype,{...});
 * The primary changes are as follows:
 * 1) Modify commitChanges() to push all changes to the store if one is available, defined as:
 * 2) Add updateProxy config option. If this is not null, then commitChanges() will call:
 * 3) Add write() function with options to write changes.
 * 4) Add replaceWrite config boolean option. If set to true, then commitChanges() pushes
 *    the entire set of data out, rather than just the changes.
 *
 * Additionally, the old modified[] list has been replaced by a journal[] that records
 * all changes: add, remove, insert, update of any record. This allows for complete
 * commit or rollback, and pushing changes appropriately to the server.
 *
 * Additionally, the reader must support the update option. In order to do so,
 * we have provided a modification of Ext.data.JsonReader. Ext.data.XmlReader will
 * come later.
 */
Ext.ux.WriteStore = Ext.extend(Ext.data.Store,
    {
        constructor: function (config) {
            this.myclass = Ext.ux.WriteStore;
            this.modes = this.myclass.modes;
            this.types = this.myclass.types;
            this.superclass = this.myclass.superclass;
            this.superclass.constructor.call(this, config);

            // 'this' reference to constants
            this.modes = this.myclass.modes;
            this.types = this.myclass.types;
            this.updates = this.myclass.updates;

            // add our special events
            this.addEvents(
                'beforewrite',
                'write',
                'writeexception',
                'commit'
            );

            if (this.updateProxy) {
                this.relayEvents(this.updateProxy, ["updateexception"]);
            }

            // make sure each load cleans the journal
            this.on('load', function () {
                this.journal = [];
            });

            // default writeMode, updateMode
            this.defaultWriteMode = this.modes.update;
            this.defaultUpdateMode = false;
        },

        // to keep track of changes
        journal: [],
        maxId: 0,
        replaceWrite: false,

        // the added config is automatically there - these are defaults
        updateProxy: null,

        // simple duplication method
        dup: function (target, source, deep) {
            target = target || {};
            for (var prp in source) {
                target[prp] = source[prp];
            }
            return(target);
        },

        /*
         * We lightly override loadRecords so that we can ensure all our records are appropriate
         */
        loadRecords: function (o, options, success) {
            // make sure every record is an instance of what we want
            if (o && o.records) {
                this.decorateRecords(o.records);
            }
            this.superclass.loadRecords.call(this, o, options, success);
        },

        /*
         * Decorate records to be WriteRecords
         */
        decorateRecords: function (r) {
            // ensure an array
            r = [].concat(r);
            for (var i = 0; i < r.length; i++) {
                var rec = r[i];
                if ((typeof(rec)).toLowerCase() == 'object' && (!rec._type || rec._type != "Ext.ux.WriteRecord")) {
                    // decorate the actual object with the functions we want
                    new Ext.ux.WriteRecord(rec);
                }
            }
        },

        // if you want to replace all, set options.replace = true or use the store-wide option
        write: function (options) {
            if (this.fireEvent('beforewrite', this, options) != false) {
                // get the appropriate records - watch out for bad records
                var mode = this.getMode(options);
                // save the mode
                options.mode = mode;
                var records = this.writeRecords(mode);
                // structure the params we need for the update to the server
                var params = {
                    data: this.reader.write(records),
                    mode: mode
                }

                // combine the user params for this call - first the base updateParams, then
                //   the per-call params. Finally, apply any that do not override our privileged params
                //   to our params and we can send
                var p = {};
                // if updateParams have been set for this store, set them
                Ext.apply(p, this.updateParams || {});
                // if particular params have been set for this call, set them
                Ext.apply(p, options.params || {});
                Ext.applyIf(params, p);
                // add any options
                this.updateProxy.update(params, this.writeHandler, this, options);
            }
        },

        getMode: function (options) {
            // we take the method we have been asked to take: journal, journal condensed, replace all
            options = options || {};

            // begin: to support deprecated versions, should be eliminated in 2.0
            if (options.replace)
                options.mode = this.modes.replace;
            if (this.replaceWrite)
                this.writeMode = this.modes.replace;
            // end: deprecated

            // which mode will we work in? Try to use local option, then store-wide, then default
            var mode;
            if (options.mode) {
                mode = options.mode;
            } else if (this.writeMode) {
                mode = this.writeMode;
            } else if (options.replace) {
                mode = this.modes.replace;
            } else if (this.replaceWrite) {
                this.writeMode = this.modes.replace;
                mode = this.modes.replace;
            } else {
                mode = this.defaultWriteMode;
            }

            return(mode);
        },

        writeRecords: function (mode) {
            var data = [];
            var tmp;

            // include the ID, if it is part of the metadata
            var idField = this.reader.meta.id;

            // replace mode just dumps it all
            if (mode == this.modes.replace) {
                // get the actual data in the record
                var rs = this.data.getRange(0);
                for (var i = 0; i < rs.length; i++) {
                    var elm = rs[i];
                    if (elm != null) {
                        data[i] = elm.data;
                    }
                    // include the ID, if it is part of the metadata
                    if (idField) {
                        data[i][idField] = elm.id;
                    }

                }
            } else {
                // get the actual data in the record
                var recs = {};
                for (var i = 0; i < this.journal.length; i++) {
                    var entry = this.journal[i];
                    var recId = entry.record.id;
                    if (entry != null) {
                        var details = '';

                        // build our data structure - if we are in condensed mode, ensure one entry per record
                        if (mode == this.modes.condensed && recs[recId] != undefined) {
                            /*
                             * We already have an entry, so just update that one
                             * What we do here largely depends on what we want to do now
                             * - If we are updating an already updated record, we can just add updates
                             * - If we are updating a record that was added in this transaction, just change the data sent
                             * - If we are removing a record, make that the only activity
                             * - If we are adding a record, do it straight out
                             */
                            var idx = recs[recId];
                            switch (entry.type) {
                                case this.types.change:
                                    // what is the previous type?
                                    var lastType = data[idx].type;
                                    // change means just add more changes
                                    if (lastType == this.types.change) {
                                        // this should also be consolidated, since it is possible that two edits affected
                                        //  the same field. Later...
                                        data[idx].data = data[idx].data.concat(entry.data);
                                    } else if (lastType == this.types.add) {
                                        // changes to an added record, so just apply the changes to the record data we are sending
                                        for (var j = 0; j < entry.data.length; j++) {
                                            data[idx].data[entry.data[j].name] = entry.data[j]['new'];
                                        }
                                    } else if (lastType == this.types.remove) {
                                        // this makes no sense: how can I update a record that has been removed?
                                    }
                                    break;
                                case this.types.add:
                                    // this makes no sense; how can we add a record that already exists?
                                    break;
                                case this.types.remove:
                                    // just replace the update or add with a remove
                                    data[idx] = {type: entry.type, data: entry.data}
                                    break;
                            }
                        } else {
                            // we have no entry yet, so this is a new one, record it
                            recs[recId] = i;

                            // the data entry to be sent to the server
                            data[i] = {
                                type: entry.type,
                                data: entry.data,
                                rid: entry.record.id
                            };
                            // include the ID, if it is part of the metadata
                            if (idField) {
                                data[i][idField] = entry.record.id;
                            }
                        }
                    }
                }
            }
            return(data);
        },

        // handle the results
        writeHandler: function (o, success, response) {
            // if the POST worked, i.e. we reached the server and found the processing URL,
            // which handled the processing and responded, AND the processing itself succeeded,
            // then success, else exception

            // the expectation for success is that the application itself will determine it
            //  via a 'write' handler
            if (success) {
                if (this.fireEvent('write', this, o, response) != false) {
                    // update fields or even whole new records from the server
                    //  if requested either via options.update = true or this.updateResponse = true

                    // we have a few possibilities:
                    // 1) We replace all our data with that from the server - either we are in mode.replace or we explicitly
                    //    said to do so
                    // 2) We update our data with that from the server, i.e. apply journal changes
                    // which update mode will we work in? Try to use local option, then store-wide, then default
                    var update = false;
                    if (o.update) {
                        update = o.update;
                    } else if (this.updateMode) {
                        update = this.updateMode;
                    } else {
                        update = this.defaultUpdateMode;
                    }

                    if (update) {
                        /*
                         * What we would do here: we take the response, feed it to the reader, saying, "please
                         * update yourself with this data." The reader would need to know how to retrieve
                         * not only the usual data (which is given by the root or other configuration) and metadata,
                         * if relevant, but also the updates. Perhaps we can have the data given to the reader, which
                         * will then pass it back to us, but without updating the fields. This is how the initial load works.
                         * Thus, we get the data back, as records, and can then compare the records.
                         * var recs = this.reader.read(o);
                         * for each record in recs: {
                         *   if !rid {add new record}
                         *   else {
                         *     find record with this rid
                         *     if all fields in new record are null or blank, or no fields, delete existing record
                         *     for each field in new record, update field in existing record
                         *   }
                         * }
                         *
                         * NOTE: update this depends on rid getting passed through readRecords, which is not obvious
                         */

                        // if our mode was replace, then we replace everything with the server-side updates
                        var r = this.reader.read(response);
                        if (o.mode == this.modes.replace || update == this.updates.replace) {
                            this.loadRecords(r, options, true);
                        } else {
                            // we worked in journal mode, so take the changes they recommend and apply them
                            /***  BIG PROBLEM: reader.readRecords does *not* return the rid with the record. Ignore for now ***/
                            for (var i = 0; i < r.totalRecords; i++) {
                                var newRec = r.records[i];

                                var rid = 10; // *** MUST GET RID FROM SOMEWHERE

                                // do we have a record with this rid?
                                var oldRec = this.getById(rid);
                                if (rid != undefined && oldRec) {
                                    // update fields in oldRec with fields in newRec, unless all fields are blank
                                    var nullfield = 0;
                                    for (var field in newRec.data) {
                                        oldRec.data[field] = newRec.data[field];
                                        if (newRec.data[field] == null || newRec.data[field] == undefined) {
                                            nullfield++;
                                        }
                                    }
                                    // were we all null?
                                    if (newRec.data.length == 0 || newRec.data.length == nullfield) {
                                        this.remove(oldRec);
                                    }
                                } else {
                                    // we have none with this ID, so add it
                                    this.add([newRec]);
                                }
                            }
                        }

                    }

                    // commit the changes and clean out
                    var m = this.journal.slice(0);
                    // only changes need commitment, but silent for the deleted ones
                    for (var i = 0, len = m.length; i < len; i++) {
                        if (m[i].type == this.types.change) {
                            m[i].record.commit(m[i].record._deleted);
                        }
                    }
                    this.journal = [];

                    // and now that the journal is cleared, we can update fields or even whole new records from the server
                    //  if requested either via options.update = true or this.updateResponse = true
                    if (o.update || this.updateResponse) {

                    }

                    // specific success handler for this transaction
                    if (o.success && typeof(o.success) == 'function') {
                        o.success.call(this, o, response);
                    }
                    // general handler for this object
                    this.fireEvent("commit", this, o, response);
                } else {
                    if (o.failure && typeof(o.failure) == 'function') {
                        o.processfailure.call(this, o, response);
                    }
                }
            } else {
                this.fireEvent("writeexception", this, o, response);
                if (o.failure && typeof(o.failure) == 'function') {
                    o.writefailure.call(this, o, response);
                }
            }
        },

        // these all need to be modified to keep track of real changes
        add: function (records) {
            records = [].concat(records);
            this.decorateRecords(records);
            for (var i = 0, len = records.length; i < len; i++) {
                records[i].join(this);
            }
            var index = this.data.length;
            this.data.addAll(records);
            for (var i = 0; i < records.length; i++) {
                // it is important we get the data for the journal at the moment of add, as that may change over time
                this.journal.push({type: this.types.add, index: index + i, record: records[i], data: this.dup(records[i].data)});
            }
            this.fireEvent("add", this, records, index);
        },


        remove: function (record) {
            var index = this.data.indexOf(record);
            this.data.removeAt(index);
            // mark the record itself as having been deleted, so we can know if we commit it
            record._deleted = true;
            this.journal.push({type: this.types.remove, index: index, record: record, data: ''});
            this.fireEvent("remove", this, record, index);
        },


        removeAll: function () {
            // record that all objects have been removed
            for (var i = 0, len = this.data.getCount(); i < len; i++) {
                this.journal.push({type: this.types.remove, index: i, record: this.data[i], data: ''});
            }
            this.data.clear();
            this.fireEvent("clear", this);
        },


        insert: function (index, records) {
            records = [].concat(records);
            this.decorateRecords(records);
            for (var i = 0, len = records.length; i < len; i++) {
                this.data.insert(index, records[i]);
                records[i].join(this);
            }
            for (var i = 0; i < records.length; i++) {
                // it is important we get the data for the journal at the moment of add, as that may change over time
                this.journal.push({type: this.types.add, index: index + i, record: records[i], data: this.dup(records[i].data)});
            }
            this.fireEvent("add", this, records, index);
        },
        getModifiedCount: function () {
            return(this.journal.length);
        },
        isDirty: function () {
            return(this.journal.length > 0);
        },

        commitChanges: function (options) {
            options = options || {};
            if (this.updateProxy != null) {
                this.write(options);
            } else {
                this.journal.splice(0);
                this.fireEvent("commit", this, options);
            }
        },

        rejectChanges: function (count) {
            // are we rejecting all or some?
            if (!count || count >= this.journal.length) {
                count = 0;
            }

            // back out the last 'count' changes in reverse order
            // get the last 'count' elements of the journal
            var m = this.journal.splice(-count).reverse();
            for (var i = 0, len = m.length; i < len; i++) {
                var jType = m[i].type;
                if (jType == this.types.change) {
                    // reject the changes
                    m[i].record.reject(true, m[i].data.length);
                    this.fireEvent("update", this, m[i].record, Ext.data.Record.REJECT);
                } else if (jType == this.types.add) {
                    // undo the add
                    // we use the superclass remove to ensure all events work correctly
                    this.superclass.remove.call(this, m[i].record);
                } else if (jType == this.types.remove) {
                    // put it back
                    // we use the superclass remove to ensure all events work correctly
                    delete m[i].record._deleted;
                    this.superclass.insert.call(this, m[i].index, m[i].record);
                }
            }
        },

        /*
         *  The next three are for changes affected directly to a record
         *  Ideally, this should never happen: all changes go through the Store,
         *  and are passed through after being recorded. However, in an object-oriented paradigm,
         *  it is accepted that you can gain access to the direct object, unlike a SQL paradigm.
         *  Thus, we need to put events on the Record directly.
         */

        // if we edited a record directly, we need to update the journal
        afterEdit: function (record) {
            var edits = record.getEdits();
            if (edits && edits.length > 0) {
                this.journal.push({type: this.types.change, index: this.data.indexOf(record), record: record, data: edits});
            }
            this.fireEvent("update", this, record, Ext.data.Record.EDIT);
        },

        // if we rejected a change to a record directly, we need to remove it from the journal
        afterReject: function (record) {
            // find the last edit we had, and remove it
            for (var i = this.journal.length - 1; i >= 0; i++) {
                if (this.journal[i].type == this.types.change && this.journal[i].record == record) {
                    this.journal = this.journal.splice(i, 1);
                    break;
                }
            }
            this.fireEvent("update", this, record, Ext.data.Record.REJECT);
        },

        // if we committed a change to a record directly, we still keep it in the journal
        afterCommit: function (record) {
            this.fireEvent("update", this, record, Ext.data.Record.COMMIT);
        },

        getNextId: function () {
            // send back the maxId + 1
            return(this.getMaxId() + 1);
        },

        getMaxId: function () {
            var maxId = 1000;
            if (this.data != null) {
                var records = this.data.getRange(0);
                for (var i = 0; i < records.length; i++) {
                    if (records[i].id > maxId)
                        maxId = records[i].id;
                }
            }
            return(maxId);
        }
    });
Ext.apply(Ext.ux.WriteStore, {
    // fixed methods for sending data back to the server
    modes: {replace: 'r', update: 'u', condensed: 'c'},

    // fixed methods for updating the store after a response from the server
    updates: {replace: 'r', update: 'u'},

    // fixed types
    types: {change: 'u', add: 'c', remove: 'd'}
});

// XML data writer extension to Reader
Ext.ux.XmlWriterReader = Ext.extend(Ext.data.XmlReader, {
    constructor: function (meta, recordType) {
        this.superclass = Ext.ux.XmlWriterReader.superclass;
        meta = meta || {};
        this.superclass.constructor.call(this, meta, recordType || meta.fields);
    },

    // write - input objects, write out XML
    write: function (records) {

    }

});

