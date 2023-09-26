<?php

namespace App\Parser;

use Keiwen\Utils\Parsing\DirectoryParser;
use Symfony\Component\Console\Output\OutputInterface;

class MediaLibDirectoryParser extends DirectoryParser
{

    protected $output;
    protected $allowedExtensions;
    protected $ignoreBeforeTimestamp;

    public function __construct(
        string $baseDirectory,
        string $pathSeparator = '/',
        array $allowedExtensions = array(),
        OutputInterface $output = null,
        int $ignoreBeforeTimestamp = -1
    )
    {
        $this->allowedExtensions = $allowedExtensions;
        $this->output = $output;
        $this->ignoreBeforeTimestamp = $ignoreBeforeTimestamp;
        parent::__construct($baseDirectory, $pathSeparator);
    }


    protected function doesConsiderFile(string $directory, string $filename): bool
    {
        $extension = static::getFileExtension($filename);
        if (!in_array($extension, $this->allowedExtensions)) {
            if ($this->output) {
                $this->output->writeln(sprintf('   Ignored file %s%s: extension %s not allowed', $directory, $filename, $extension));
            }
            return false;
        }

        if ($this->ignoreBeforeTimestamp >= 0 && filemtime($this->baseDirectory . $directory . $filename) < $this->ignoreBeforeTimestamp) {
            if ($this->output) {
                $this->output->writeln(sprintf('   Ignored file %s%s: not modified since last execution', $directory, $filename));
            }
            return false;
        }

        return true;
    }

    protected function doesConsiderFolder(string $directory, string $foldername): bool
    {
        if ($this->ignoreBeforeTimestamp >= 0 && filemtime($this->baseDirectory . $directory . $foldername) < $this->ignoreBeforeTimestamp) {
            if ($this->output) {
                $this->output->writeln(sprintf('   Ignored folder %s%s: not modified since last execution', $directory, $foldername));
            }
            return false;
        }

        return true;
    }



}
