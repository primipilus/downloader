<?php
/**
 * @author primipilus 21.10.2016
 */

namespace primipilus\downloader;

use primipilus\downloader\exceptions\BaseException;
use primipilus\fileinfo\exceptions\BaseException as FileInfoBaseException;

/**
 * Class HttpDownloader
 * Загрузчик по http(s)
 *
 * @package primipilus\downloader
 */
class HttpDownloader extends Downloader
{

    /**
     * Downloading file to tmp dir
     *
     * @param string $fileFrom
     * @param string|null $md5
     *
     * @return DownloadedFile|null
     * @throws BaseException
     */
    public function downloadFile(string $fileFrom, string $md5 = null) : ?DownloadedFile
    {
        $original = basename($fileFrom);

        $fileTo = $this->temporaryDir . '/' . $this->createFileName($fileFrom);

        $this->saveFile($fileFrom, $fileTo);

        try {
            return new DownloadedFile($fileTo, $original);
        } catch (FileInfoBaseException $e) {
        }

        return null;
    }

    protected function saveFile(string $fileFrom, string $fileTo) : void
    {
        $ch = curl_init($fileFrom);
        $fp = fopen($fileTo, 'wb');
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);
    }
}