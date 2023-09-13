<?php

namespace App\Parser;

use Keiwen\Utils\Parsing\DirectoryParser;
use Symfony\Component\Console\Output\OutputInterface;

class MediaLibDirectoryParser extends DirectoryParser
{

    protected $output;
    protected $allowedExtensions;

    public function __construct(
        string $baseDirectory,
        string $pathSeparator = '/',
        array $allowedExtensions = array(),
        OutputInterface $output = null
    )
    {
        $this->allowedExtensions = $allowedExtensions;
        $this->output = $output;
        parent::__construct($baseDirectory, $pathSeparator);
        // TODO include starting time and check if file/folder is after or not
    }


    protected function doesConsiderFile(string $directory, string $filename): bool
    {
        $extension = static::getFileExtension($filename);
        if (!in_array($extension, $this->allowedExtensions)) {
            if ($this->output) {
                $this->output->writeln(sprintf('   Ignored file %s%s as extension %s not allowed', $directory, $filename, $extension));
            }
            return false;
        }

        //TODO check modified date with filemtime($this->baseDirectory . $directory . $filename)

        return true;
    }

    protected function doesConsiderFolder(string $directory, string $foldername): bool
    {
        //TODO check modified date with filemtime($this->baseDirectory . $directory . $foldername)

        return true;
    }



}
