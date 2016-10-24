<?php
/**
 * @author primipilus 21.10.2016
 */

namespace primipilus\downloader;

/**
 * Class HttpDownloader
 * Загрузчик по http(s)
 *
 * @package primipilus\downloader
 */
class HttpDownloader extends Downloader
{

    /**
     * Save file from source to fileTo
     *
     * @param string $fileFrom
     * @param string $fileTo
     */
    protected function saveFile(string $fileFrom, string $fileTo) : void
    {
        if ($fp = @fopen($fileTo, 'wb')) {
            $ch = curl_init($fileFrom);
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
            curl_exec($ch);
            curl_close($ch);
            fclose($fp);
        }
    }
}