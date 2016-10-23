Downloader
--------

Composer install
----------------

```bash
composer require "primipilus/downloader:~1.0"
```

Usage
-----

```php
$downloader = \primipilus\downloader\Downloader::createDownloader('http', ['temporaryDir' => '/tmp']);

$file = $downloader->downloadFile($fileFrom);

echo $file->getOriginal(), PHP_EOL;
echo $file->getInfo()->path, PHP_EOL;
echo $file->getInfo()->basename, PHP_EOL;
echo $file->getInfo()->filename, PHP_EOL;
echo $file->getInfo()->extension, PHP_EOL;
if ($file->getInfo()->isImage) {
    echo $file->getInfo()->image->width . ' px', PHP_EOL;
    echo $file->getInfo()->image->height . ' px', PHP_EOL;
}
```