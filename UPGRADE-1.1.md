1.1.0
=====

DataSourceBundle
----------------

The Data Source Bundle has been removed and split into a new package.

Remove the Data Source Bundle from your AppKernel und admin_routing.yml. If required, install the new phlexible/suggest-bundle.

If you have no need for the suggest bundle, remove the old datasource tables:

```
DROP TABLE datasource;
DROP TABLE datasource_value;
```

ElementBundle
-------------

The foreign key of the element meta table has changed.

Use these queries in your migrations: 

```
ALTER TABLE element_meta DROP FOREIGN KEY FK_94CC35CC4FBDA576;
DROP INDEX IDX_94CC35CC4FBDA576 ON element_meta;
ALTER TABLE element_meta ADD element_version_id INT DEFAULT NULL;
UPDATE element_meta em, element_version ev SET em.element_version_id = ev.id WHERE em.eid = ev.eid AND em.version = ev.version;
ALTER TABLE element_meta DROP version;
ALTER TABLE element_meta ADD CONSTRAINT FK_94CC35CCE7A8D56B FOREIGN KEY (element_version_id) REFERENCES element_version (id) ON DELETE CASCADE;
CREATE INDEX IDX_94CC35CCE7A8D56B ON element_meta (element_version_id);
```
     
FrontendBundle
--------------

The Frontend Bundle has been merged into the CMS Bundle.

```
# admin_routing.yml
  
# before
phlexible_frontend_preview:
    resource: "@PhlexibleFrontendBundle/Controller/PreviewController.php"
    type:     annotation
 
# after
phlexible_cms_preview:
    resource: "@PhlexibleCmsBundle/Controller/PreviewController.php"
    type:     annotation
```

Dependencies
------------

The dependency to igorw/file-server-bundle has been completely removed.

Remove the following line from your AppKernel:

```php
    new Igorw\FileServeBundle\IgorwFileServeBundle(),
```

Remove the follow section from your config.yml:

```
# Igorw File Serve Bundle
igorw_file_serve:
    factory: php # php
    #factory: sendfile # nginx
    #factory: xsendfile # apache
```
