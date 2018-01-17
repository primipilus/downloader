<?php
/**
 * @author primipilus 21.10.2016
 */

use primipilus\downloader\exceptions\BaseException;

require dirname(__DIR__) . '/vendor/autoload.php';

try {
    $downloader = \primipilus\downloader\Downloader::getInstance('http', ['temporaryDir' => __DIR__, 'attempts' => 5]);
} catch (BaseException $e) {
    echo $e->getMessage(), PHP_EOL;
    exit();
}
$files = [
    'http://guilher.me/wp-content/uploads/2009/05/vira-lata-preto.jpg',
    'https://images-na.ssl-images-amazon.com/images/G/01/img15/pet-products/small-tiles/23695_pets_vertical_store_dogs_small_tile_8._CB312176604_.jpg',
];
foreach ($files as $filePath) {
    if ($file = $downloader->downloadFile($filePath, '2c889c99da4511249b33dc1c99d13754')) {
        echo 'original: ', $file->getOriginal(), PHP_EOL;
        echo 'path: ', $file->getInfo()->path, PHP_EOL;
        echo 'md5: ', $file->getInfo()->basename, PHP_EOL;
        echo 'basename: ', $file->getInfo()->filename, PHP_EOL;
        echo 'extension: ', $file->getInfo()->extension, PHP_EOL;
        echo 'md5: ', $file->getInfo()->md5, PHP_EOL;
        echo 'sha1: ', $file->getInfo()->sha1, PHP_EOL;
        if ($file->getInfo()->isImage) {
            echo 'file is image', PHP_EOL;
            echo "\t width: ", $file->getInfo()->image->width . ' px', PHP_EOL;
            echo "\t height: ", $file->getInfo()->image->height . ' px', PHP_EOL;
        }

        @unlink($file->getInfo()->path);
    } else {
        echo "File {$filePath} is not download", PHP_EOL;
    }
}

// without md5 or sha1
$file = $downloader->downloadFile('https://www.domain.not.exist/file_not_exist.jpg');
if (is_null($file)) {
    echo 'File not download', PHP_EOL;
}