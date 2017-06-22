<?php
/**
 * @author primipilus 21.10.2016
 */

namespace primipilus\downloader;

use primipilus\downloader\exceptions\BaseException;
use primipilus\fileinfo\exceptions\BaseException as FileInfoBaseException;
use ReflectionClass;
use ReflectionException;

/**
 * The Base Class Downloader
 *
 * Базовый класс зашрузчик
 *
 * @property-read string $temporaryDir
 *
 * @package primipilus\downloader
 */
abstract class Downloader
{

    /**
     * @var array list of built-in downloaders
     */
    private static $builtInDownloaders = [
        'http'  => HttpDownloader::class,
        'ftp'   => FtpDownloader::class,
    ];

    /**
     * @var Downloader[] array of downloaders
     */
    private static $downloaders = [];

    /** @var string */
    private $_temporaryDir = '/tmp';
    /** @var integer */
    private $_attempts = 5;

    /**
     * Get Singleton instance of Downloader
     *
     * @param string $type ClassName or Alias for Downloader
     * @param array $config config of (key, value) ['temporaryDir' => '/tmp']
     *
     * @return Downloader
     * @throws BaseException
     */
    final public static function getInstance(string $type, array $config = []) : Downloader
    {
        if (isset(self::$builtInDownloaders[$type])) {
            $type = self::$builtInDownloaders[$type];
        }

        if (!isset(self::$downloaders[$type])) {
            self::$downloaders[$type] = self::createInstance($type, $config);
        }

        return self::$downloaders[$type];
    }

    /**
     * Create new instance of Downloader
     *
     * @param string $type ClassName or Alias for Downloader
     * @param array $config config of (key, value) ['temporaryDir' => '/tmp']
     *
     * @return Downloader
     * @throws BaseException
     */
    final public static function createInstance(string $type, array $config = []) : Downloader
    {
        if (isset(self::$builtInDownloaders[$type])) {
            $type = self::$builtInDownloaders[$type];
        } else {
            if (!class_exists($type)) {
                throw new BaseException('Class ' . $type . ' not exists');
            }
            try {
                $reflector = new ReflectionClass($type);
                if (!$reflector->isSubclassOf(__CLASS__)) {
                    throw new BaseException('Class ' . $type . ' is not instance of ' . __CLASS__);
                }
            } catch (ReflectionException $e) {
                throw new BaseException($e->getMessage(), $e->getCode(), $e);
            }
        }

        $downloader = new $type;
        foreach ($config as $key => $value) {
            $downloader->{$key} = $value;
        }

        return $downloader;
    }

    /**
     * Downloading file to temporaryDir
     *
     * @param string $fileFrom
     * @param string|null $md5
     * @param string|null $sha1
     * @return DownloadedFile|null
     */
    public function downloadFile(string $fileFrom, ?string $md5 = null, ?string $sha1 = null) : ?DownloadedFile
    {
        $original = basename($fileFrom);

        $fileTo = rtrim($this->_temporaryDir, '/') . '/' . $this->createFileName($fileFrom);

        for ($i = 0; $i < $this->_attempts; $i++) {
            $this->saveFile($fileFrom, $fileTo);

            try {
                $file = new DownloadedFile($fileTo, $original);
                if ((is_null($md5) || $md5 === $file->getInfo()->md5) and (is_null($sha1) || $sha1 === $file->getInfo()->sha1)) {
                    return $file;
                } else {
                    @unlink($fileTo);
                }
            } catch (FileInfoBaseException $e) {
            }
        }

        return null;
    }

    /**
     * create path for new file
     *
     * @param string $fileFrom
     *
     * @return string
     */
    protected function createFileName(string $fileFrom) : string
    {
        $extension = strtolower(pathinfo($fileFrom, PATHINFO_EXTENSION));

        return microtime(true) . sha1($fileFrom) . '.' . $extension;
    }

    /**
     * Save file from source to fileTo
     *
     * @param string $fileFrom
     * @param string $fileTo
     */
    abstract protected function saveFile(string $fileFrom, string $fileTo) : void;

    /**
     * @return string
     */
    public function getTemporaryDir() : string
    {
        return $this->_temporaryDir;
    }

    /**
     * @param string $temporaryDir
     *
     * @throws BaseException
     */
    public function setTemporaryDir(string $temporaryDir) : void
    {
        if (!file_exists($temporaryDir)) {
            throw new BaseException('Dir ' . $temporaryDir . ' is not exists');
        }
        if (!is_dir($temporaryDir)) {
            throw new BaseException('Path ' . $temporaryDir . ' is not a dir');
        }

        $this->_temporaryDir = $temporaryDir;
    }

    /**
     * @inheritdoc
     */
    public function __get($name)
    {
        if (method_exists($this, 'get' . $name)) {
            return call_user_func([$this, 'get' . $name]);
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function __set($name, $value)
    {
        if (method_exists($this, 'set' . $name)) {
            call_user_func([$this, 'set' . $name], $value);
        } else {
            throw new BaseException('property ' . $name . ' does not exists');
        }
    }

    /**
     * @return int
     */
    public function getAttempts(): int
    {
        return $this->_attempts;
    }

    /**
     * @param int $attempts
     */
    public function setAttempts(int $attempts)
    {
        $this->_attempts = $attempts > 0 ? $attempts : 1;
    }

}