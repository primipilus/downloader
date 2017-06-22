<?php

namespace primipilus\downloader;

use FtpClient\FtpClient;
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
    private $_ftpClient;

    /**
     * @return FtpClient
     */
    public function getFtpClient() : FtpClient
    {
        return $this->_ftpClient;
    }

    /**
     * @param FtpClient $ftpClient
     */
    public function setFtpClient(FtpClient $ftpClient)
    {
        $this->_ftpClient = $ftpClient;
    }

    /**
     * @param string $fileFrom
     * @param string $fileTo
     *
     * @throws BaseException
     */
    protected function saveFile(string $fileFrom, string $fileTo) : void
    {
        if (!@$this->_ftpClient->get($fileTo, $fileFrom, FTP_BINARY)) {
            throw new BaseException("File " . $fileFrom . " not exists");
        }
    }

}