<?php
namespace Mihaeu;

/**
 * Represents a file
 */
class File
{
    /**
     * @var \SplFileObject
     */
    protected $movieFile;

    /**
     * @var string
     */
    protected $fileExtension;

    public function __construct($movieFile)
    {
        $this->movieFile = new \SplFileObject($movieFile);
        $this->fileExtension = $this->movieFile->getExtension();
    }

    /**
     * @return string
     */
    public function getExtension()
    {
        return $this->fileExtension;
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->movieFile->getPath().DIRECTORY_SEPARATOR.$this->movieFile->getBasename();
    }

}
