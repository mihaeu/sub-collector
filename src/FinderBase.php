<?php

namespace Mihaeu\SubCollector;

abstract class FinderBase
{
    /**
     * @var string
     */
    protected $directory;

    /**
     * @var string
     */
    protected $createObject;

    /**
     * @var array
     */
    protected $fileExtensions = array();

    public function setFileExtensions($fileExtensions)
    {
        $this->fileExtensions = $fileExtensions;
    }

    /**
     * $s name of class inherited from \Mihaeu\File
     */
    public function setCreateObject($s)
    {
        $this->createObject = $s;
    }

    /**
     * Finds all files in a folder
     *
     * @return array
     */
    public function findFilesInFolder()
    {
        if (is_file($this->directory))
        {
            return array(new $this->createObject(realpath($this->directory)));
        }

        if ( ! is_dir($this->directory))
        {
            throw new \InvalidArgumentException($this->directory.' is not a directory.');
        }

        $fileIterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->directory),
            \RecursiveIteratorIterator::SELF_FIRST
        );
        $result = array();
        foreach ($fileIterator as $key => $value)
        {
            if (is_dir($key) || !is_readable($key))
            {
                continue;
            }

            $file = null;
            try {
                $file = new $this->createObject($key);
            } catch (\RuntimeException $e) {
                // don't process system files
            }

            if ($file !== null
                && in_array($file->getExtension(), $this->fileExtensions))
            {
                $result[] = $file;
            }

        }
        return $result;
    }

    /**
     * @param string $directory
     */
    public function setDirectory($directory)
    {
        $this->directory = $directory;
    }
}
