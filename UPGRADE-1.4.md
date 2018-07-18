# 1.4.0

## GuiBundle

Config Changes:

- The section **phlexible_gui.mail** has been removed, required changes:
  - **phlexible_gui.mail.from_name** => **phlexible_messsage.from_email.sender_name**
  - **phlexible_gui.mail.from_email** => **phlexible_messsage.from_email.address**
  - **phlexible_gui.mail.from_name** => **phlexible_user.from_email.sender_name**
  - **phlexible_gui.mail.from_email** => **phlexible_user.from_email.address**
  
## MediaToolBundle

Required config changes:

- If set, remove the section **phlexible_media_tool.swftools**.
  This configuration is not used anymore.
- If set, remove the section **phlexible_media_tool.mime**.
  This configuration is not used anymore.
- Unless customized, change the configuration of **phlexible_media_cache.storages** to:
  ```yaml
  phlexible_media_cache:
      storages:
          default:
            driver: local
            storage_dir: "%media_cache_storage_dir%"
  ```
Config changes:

- The following sections are now optional:
  - **phlexible_media_tool.exiftool**
    - If you want to use the exiftoool services, you need to add the required library to your composer.json:
      - composer require alchemy/phpexiftool
  - **phlexible_media_tool.ffmpeg**
    - If you want to use the ffmpeg services, you need to add the required library to your composer.json:
      - composer require php-ffmpeg/php-ffmpeg
      - composer require php-ffmpeg/extras
  - **phlexible_media_tool.poppler**
    - If you want to use the poppler services, you need to add the required library to your composer.json:
      - composer require php-poppler/php-poppler

## MediaManagerBundle

File versioning has been completely revamped.

Required migration:

```
CREATE TABLE media_file_version (id CHAR(36) NOT NULL, version INT DEFAULT 1 NOT NULL, file_id CHAR(36) DEFAULT NULL, media_category VARCHAR(255) NOT NULL, media_type VARCHAR(255) NOT NULL, metasets LONGTEXT DEFAULT NULL COMMENT '(DC2Type:simple_array)', name VARCHAR(255) NOT NULL, mime_type VARCHAR(100) NOT NULL, hash CHAR(32) NOT NULL, size INT NOT NULL, attributes LONGTEXT DEFAULT NULL COMMENT '(DC2Type:json_array)', create_user_id CHAR(36) NOT NULL, created_at DATETIME NOT NULL, modify_user_id CHAR(36) NOT NULL, modified_at DATETIME NOT NULL, INDEX IDX_8D8BC07493CB796C (file_id), PRIMARY KEY(id, version)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
INSERT INTO media_file_version SELECT UUID(), version, id, media_category, media_type, metasets, name, mime_type, hash, size, attributes, create_user_id, created_at, modify_user_id, modified_at FROM media_file;
DELETE FROM media_file WHERE version > 1;
DELETE FROM media_file_usage WHERE file_version > 1;
DELETE FROM media_file_meta WHERE file_version > 1;
ALTER TABLE media_file_meta DROP FOREIGN KEY FK_7B6ECFD593CB796CE47A6AF8;
DROP INDEX IDX_7B6ECFD593CB796CE47A6AF8 ON media_file_meta;
ALTER TABLE media_file_meta DROP file_version;
CREATE INDEX IDX_7B6ECFD593CB796C ON media_file_meta (file_id);
ALTER TABLE media_file_usage DROP FOREIGN KEY FK_704D20D393CB796CE47A6AF8;
DROP INDEX IDX_704D20D393CB796CE47A6AF8 ON media_file_usage;
ALTER TABLE media_file_usage DROP file_version;
CREATE INDEX IDX_704D20D393CB796C ON media_file_usage (file_id);
ALTER TABLE media_file DROP PRIMARY KEY;
ALTER TABLE media_file ADD PRIMARY KEY (id);
ALTER TABLE media_file_version ADD CONSTRAINT FK_8D8BC07493CB796C FOREIGN KEY (file_id) REFERENCES media_file (id);
ALTER TABLE media_file_usage ADD CONSTRAINT FK_704D20D393CB796C FOREIGN KEY (file_id) REFERENCES media_file (id) ON DELETE CASCADE;
ALTER TABLE media_file_meta ADD CONSTRAINT FK_7B6ECFD593CB796C FOREIGN KEY (file_id) REFERENCES media_file (id) ON DELETE CASCADE;


ALTER TABLE media_file_version DROP PRIMARY KEY;
ALTER TABLE media_file_version DROP FOREIGN KEY FK_8D8BC07493CB796C;
ALTER TABLE media_file_version DROP id, CHANGE file_id file_id CHAR(36) NOT NULL, CHANGE version file_version INT DEFAULT 1 NOT NULL;
ALTER TABLE media_file_version ADD PRIMARY KEY (file_id, file_version);
ALTER TABLE media_file_version ADD CONSTRAINT FK_8D8BC07493CB796C FOREIGN KEY (file_id) REFERENCES media_file (id);
```