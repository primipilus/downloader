<?php

namespace primipilus\downloader;

use FtpClient\FtpClient;
use primipilus\downloader\exceptions\BaseException;

/**
 * Class FtpDownloader
 * Загрузчик по ftp
 *
 * @property FtpClient $client
 *
 * @package primipilus\downloader
 */
class FtpDownloader extends Downloader
{

    /** @var FtpClient */
    private $_client;

    /**
     * @return FtpClient
     */
    public function getClient() : FtpClient
    {
        return $this->_client;
    }

    /**
     * @param FtpClient $ftpClient
     */
    public function setClient(FtpClient $ftpClient)
    {
        $this->_client = $ftpClient;
    }

    /**
     * @param string $fileFrom
     * @param string $fileTo
     *
     * @throws BaseException
     */
    protected function saveFile(string $fileFrom, string $fileTo) : void
    {
        try {
            $result = $this->_client->get($fileTo, $fileFrom, FTP_BINARY);
        } catch (\Exception|\Error $e) {
            throw new BaseException("Error download file " . $fileFrom, 0, $e);
        }
        if (!$result) {
            throw new BaseException("File " . $fileFrom . " not exists");
        }
    }

}