1.3.0
=====

ElementBundle
-------------

Required schema changes:

```sql
ALTER TABLE element_meta DROP eid;
```

ElementtypeBundle
-----------------

Required schema changes:

```sql
CREATE INDEX IDX_DB56ECEB4A1A35C0BF1CD3C3D4DB71B5 ON element_structure_value (ds_id, version, language);
```
