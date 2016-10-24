<?php
/**
 * @author primipilus 21.10.2016
 */

use primipilus\downloader\exceptions\BaseException;

require dirname(__DIR__) . '/vendor/autoload.php';

try {
    $downloader = \primipilus\downloader\Downloader::getInstance('http', ['temporaryDir' => __DIR__]);
} catch (BaseException $e) {
    echo $e->getMessage(), PHP_EOL;
    exit();
}
$files = [
    'http://guilher.me/wp-content/uploads/2009/05/vira-lata-preto.jpg',
    'https://images-na.ssl-images-amazon.com/images/G/01/img15/pet-products/small-tiles/23695_pets_vertical_store_dogs_small_tile_8._CB312176604_.jpg',
];
foreach ($files as $file) {
    if ($file = $downloader->downloadFile($file)) {
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
    } else {
        var_dump($file);
    }
}