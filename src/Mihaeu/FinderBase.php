<?php
namespace Mihaeu;

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

            $movieFile = null;
            try {
                $movieFile = new $this->createObject($key);
            } catch (\RuntimeException $e) {
                // don't process system files
            }

            if ($movieFile !== null
                && in_array($movieFile->getExtension(), $this->fileExtensions))
            {
                $result[] = $movieFile;
            }

        }
        return $result;
    }

    /**
     * @param string $directory
     */
    public function setDirectory($directory)
    {
        if ( ! is_dir($directory))
        {
            throw new \InvalidArgumentException($directory.' is not a directory.');
        }
        $this->directory = $directory;
    }
}
