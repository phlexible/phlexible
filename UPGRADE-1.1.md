1.1.0
=====

DataSourceBundle
----------------

The Data Source Bundle has been removed and split into a new package.

Remove the Data Source Bundle from your AppKernel und admin_routing.yml. If required, install the new phlexible/suggest-bundle.

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
