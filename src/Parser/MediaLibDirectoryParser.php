<?php

namespace App\Parser;

use Keiwen\Utils\Parsing\DirectoryParser;
use Symfony\Component\Console\Output\OutputInterface;

class MediaLibDirectoryParser extends DirectoryParser
{

    protected $output;
    protected $allowedExtensions;
    protected $ignoreBeforeTimestamp;

    protected $ignoredByExtensions = array();
    protected $ignoredByTimestamp = array();

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
        $extension = strtolower(static::getFileExtension($filename));
        if (!in_array($extension, $this->allowedExtensions)) {
            if ($this->output) {
                $this->output->writeln(sprintf('   Ignored file %s%s: extension %s not allowed', $directory, $filename, $extension));
            }
            if (!isset($this->ignoredByExtensions[$extension])) $this->ignoredByExtensions[$extension] = array();
            $this->ignoredByExtensions[$extension][] = $directory . $filename;
            return false;
        }

        if ($this->ignoreBeforeTimestamp >= 0 && filemtime($this->baseDirectory . $directory . $filename) < $this->ignoreBeforeTimestamp) {
            if ($this->output) {
                $this->output->writeln(sprintf('   Ignored file %s%s: not modified since last execution', $directory, $filename));
            }
            $this->ignoredByTimestamp[] = $directory . $filename;
            return false;
        }

        return true;
    }

    /**
     * @return array extension => list of filepath
     */
    public function getIgnoredByExtensions(): array
    {
        return $this->ignoredByExtensions;
    }

    /**
     * @return array filepaths
     */
    public function getIgnoredByTimestamp(): array
    {
        return $this->ignoredByTimestamp;
    }

}
