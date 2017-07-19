1.4.0
=====

MediaToolBundle
---------------

Required config changes:

- Remove the section **phlexible_media_tool.swftools**.
  This configuration is not used anymore.

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
