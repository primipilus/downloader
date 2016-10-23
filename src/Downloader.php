<?php
/**
 * @author primipilus 21.10.2016
 */

namespace primipilus\downloader;

use primipilus\downloader\exceptions\BaseException;
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
        'http' => HttpDownloader::class,
    ];

    /**
     * @var Downloader[] array of downloaders
     */
    private static $downloaders = [];

    /** @var string */
    private $_temporaryDir = '/tmp';
    /** @var integer */
    private $_dirPermissions = 0775;
    /** @var integer */
    private $_filePermissions = 0664;

    /**
     * @param string $type ClassName or Alias for Downloader
     *
     * @param array $config config of (key, value) ['temporaryDir' => '/tmp']
     *
     * @return Downloader
     * @throws BaseException
     */
    final public static function createDownloader(string $type, array $config = []) : Downloader
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

        if (!isset(self::$downloaders[$type])) {
            self::$downloaders[$type] = new $type;
            foreach ($config as $key => $value) {
                // todo change to set method
                self::$downloaders[$type]->{$key} = $value;
            }
        }

        return self::$downloaders[$type];
    }

    /**
     * Downloading file to tmp dir
     *
     * @param string $fileFrom
     * @param string|null $md5
     *
     * @return DownloadedFile|null
     * @throws BaseException
     */
    abstract public function downloadFile(string $fileFrom, string $md5 = null) : ?DownloadedFile;

    /**
     * @return string
     */
    public function getTemporaryDir() : string
    {
        return $this->_temporaryDir;
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
        }
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
     * @param string $temporaryDir
     *
     * @throws BaseException
     */
    private function setTemporaryDir(string $temporaryDir) : void
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
     * @param string $permissions
     */
    private function setDirPermissions(string $permissions) : void
    {
        $this->_dirPermissions = $permissions;
    }

    /**
     * @param string $permissions
     */
    private function setFilePermissons(string $permissions) : void
    {
        $this->_filePermissions = $permissions;
    }
}