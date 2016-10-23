<?php
/**
 * @author primipilus 21.10.2016
 */

require dirname(__DIR__) . '/vendor/autoload.php';

$downloader = \primipilus\downloader\Downloader::createDownloader('http', ['temporaryDir' => __DIR__]);

$file = $downloader->downloadFile('http://guilher.me/wp-content/uploads/2009/05/vira-lata-preto.jpg');

echo $file->getOriginal(), PHP_EOL;
echo $file->getInfo()->path, PHP_EOL;
echo $file->getInfo()->basename, PHP_EOL;
echo $file->getInfo()->filename, PHP_EOL;
echo $file->getInfo()->extension, PHP_EOL;
if ($file->getInfo()->isImage) {
    echo $file->getInfo()->image->width . ' px', PHP_EOL;
    echo $file->getInfo()->image->height . ' px', PHP_EOL;
}

@unlink($file->getInfo()->path);