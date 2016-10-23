<?php
/**
 * @author primipilus 23.10.2016
 */

namespace primipilus\downloader;

use primipilus\fileinfo\exceptions\BaseException as FileInfoBaseException;
use primipilus\fileinfo\FileInfo;

/**
 * Class DownloadedFile
 *
 * @package primipilus\downloader
 */
class DownloadedFile
{

    /** @var string */
    protected $_original;
    /** @var FileInfo */
    protected $_info;

    /**
     * FileInfo constructor.
     *
     * @param string $path
     * @param string $original
     *
     * @throws FileInfoBaseException
     */
    public function __construct(string $path, string $original)
    {
        $this->_original = $original;
        $this->_info = new FileInfo($path);
    }

    /**
     * @return string
     */
    public function getOriginal() : string
    {
        return $this->_original;
    }

    /**
     * @return FileInfo
     */
    public function getInfo() : FileInfo
    {
        return $this->_info;
    }
}