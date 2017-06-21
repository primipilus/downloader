<?php

namespace fs\core\common\helpers\file;

use primipilus\downloader\Downloader;
use primipilus\downloader\exceptions\BaseException;

/**
 * Class FtpDownloader
 * Загрузчик по ftp
 *
 * @package primipilus\downloader
 */
class FtpDownloader extends Downloader
{
    /** @var  FtpClient */
    protected $ftpClient;

    /**
     * @param string $fileFrom
     * @param string $fileTo
     *
     * @throws BaseException
     */
    protected function saveFile(string $fileFrom, string $fileTo): void
    {
        if (!@$this->ftpClient->get($fileTo, $fileFrom, FTP_BINARY)) {
            throw new BaseException("File " . $fileFrom . " not exists");
        }
    }

}