<?php
/**
 * @author primipilus 21.10.2016
 */

namespace primipilus\downloader;

use primipilus\downloader\exceptions\BaseException;

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
     *
     * @throws BaseException
     */
    protected function saveFile(string $fileFrom, string $fileTo) : void
    {
        if ($fp = @fopen($fileTo, 'wb')) {
            $errorText = null;
            $ch = curl_init($fileFrom);
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
            curl_exec($ch);
            if (curl_errno($ch)) {
                $errorText = curl_error($ch);
            }
            $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            curl_close($ch);
            @fclose($fp);

            if ($errorText) {
                @unlink($fileTo);
                throw new BaseException("Cant download file {$fileFrom}: $errorText");
            }
            if ($statusCode != 200) {
                @unlink($fileTo);
                throw new BaseException("Cant download file {$fileFrom}: code {$statusCode}");
            }
        } else {
            throw new BaseException("cant create file {$fileTo}");
        }
    }
}