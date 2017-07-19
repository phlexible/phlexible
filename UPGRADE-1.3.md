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

MediaManagerBundle
------------------

Required schema changes:

```sql
ALTER TABLE media_file_meta DROP FOREIGN KEY FK_7B6ECFD593CB796CE47A6AF8;
ALTER TABLE media_file_meta ADD CONSTRAINT FK_7B6ECFD593CB796CE47A6AF8 FOREIGN KEY (file_id, file_version) REFERENCES media_file (id, version) ON DELETE CASCADE;
ALTER TABLE media_file_usage DROP FOREIGN KEY FK_704D20D393CB796CE47A6AF8;
ALTER TABLE media_file_usage ADD CONSTRAINT FK_704D20D393CB796CE47A6AF8 FOREIGN KEY (file_id, file_version) REFERENCES media_file (id, version) ON DELETE CASCADE;
ALTER TABLE media_folder_meta DROP FOREIGN KEY FK_390540B3162CB942;
ALTER TABLE media_folder_meta ADD CONSTRAINT FK_390540B3162CB942 FOREIGN KEY (folder_id) REFERENCES media_folder (id) ON DELETE CASCADE;
ALTER TABLE media_folder_usage DROP FOREIGN KEY FK_D4DE8F31162CB942;
ALTER TABLE media_folder_usage ADD CONSTRAINT FK_D4DE8F31162CB942 FOREIGN KEY (folder_id) REFERENCES media_folder (id) ON DELETE CASCADE;
```

Maintenance
-----------

These commands should be called in this order to clear some inconsistencies:

```bash
$ php bin/console element:generate:hashes
$ php bin/console element:generate:links
$ php bin/console element:generate:mapped-fields
$ php bin/console media-manager:read
$ php bin/console frontend-media-manager:update-usage
```
