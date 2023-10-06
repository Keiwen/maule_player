<?php

namespace App\Command;

use App\Parser\MediaLibDirectoryParser;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DetectExtensionCommand extends Command
{

    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:detect-extension';
    // the command description shown when running "php bin/console list"
    protected static $defaultDescription = 'List files with specified extension from media library folder';

    protected $medialibFolder;
    protected $pathSeparator;
    protected $startingDateTime;
    protected $searchForExtension = '';

    public function __construct(
        string $medialibFolder,
        string $pathSeparator = '/',
        string $name = null
    )
    {
        parent::__construct($name);
        $this->medialibFolder = $medialibFolder;
        $this->pathSeparator = $pathSeparator;
    }


    protected function configure(): void
    {
        $this->setHelp('This command allows you to list files with specified extension from media library folders');
        $this->addArgument('extension', InputArgument::REQUIRED, 'Targeted extension');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->startingDateTime = new \DateTime();
        $this->searchForExtension = strtolower($input->getArgument('extension'));
        $output->writeln(sprintf('Start analyzing for %s files in mediaLib directory: %s',
            $this->searchForExtension, $this->medialibFolder));

        $dirParser = new MediaLibDirectoryParser(
            $this->medialibFolder,
            $this->pathSeparator,
            array($this->searchForExtension)
        );
        $filesCount = $dirParser->getFilesCount();

        $allFiles = $dirParser->getAllFiles();
        foreach ($allFiles as $folder => $filesInFolder) {
            $output->writeln(sprintf('Folder %s:', $folder));
            foreach ($filesInFolder as $fullPathToFile) {
                $fileRelPath = str_replace($this->medialibFolder, '', $fullPathToFile);
                $filename = str_replace($folder, '', $fileRelPath);
                $output->writeln(sprintf('     %s', $filename));
            }
        }

        $executionTime = time() - $this->startingDateTime->getTimestamp();
        $output->writeln(sprintf('List %d files in %d seconds', $filesCount, $executionTime));

        return Command::SUCCESS;
    }

}
