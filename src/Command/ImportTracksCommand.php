<?php

namespace App\Command;

use App\Entity\Album;
use App\Entity\Artist;
use App\Entity\Track;
use App\Parser\MediaLibDirectoryParser;
use App\Repository\AlbumRepository;
use App\Repository\ArtistRepository;
use App\Repository\TrackRepository;
use Doctrine\Persistence\ManagerRegistry;
use Keiwen\Cacofony\EntitiesManagement\EntityRegistry;
use wapmorgan\Mp3Info\Mp3Info;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportTracksCommand extends Command
{

    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:import-tracks';
    // the command description shown when running "php bin/console list"
    protected static $defaultDescription = 'Import tracks from media library folder';

    protected $managerRegistry;
    protected $entityRegistry;
    protected $medialibFolder;
    protected $pathSeparator;
    protected $candidateArtist = array();
    protected $candidateAlbums = array();
    protected $candidateTracks = array();
    protected $candidatesTree = array();
    protected $persistedArtists = array();
    protected $persistedAlbums = array();
    protected $persistedTracks = array();

    public function __construct(
        ManagerRegistry $registry,
        EntityRegistry $entityRegistry,
        string $medialibFolder,
        string $pathSeparator = '/',
        string $name = null
    )
    {
        parent::__construct($name);
        $this->managerRegistry = $registry;
        $this->entityRegistry = $entityRegistry;
        $this->medialibFolder = $medialibFolder;
        $this->pathSeparator = $pathSeparator;
    }


    protected function configure(): void
    {
        $this->setHelp('This command allows you to import files in media library folders');
        $this
            ->addOption(
                // this is the name that users must type to pass this option (e.g. --test)
                'no-db',
                // this is the optional shortcut of the option name, which usually is just a letter
                // (e.g. `t`, so users pass it as `-t`); use it for commonly used options
                // or options with long names
                null,
                // this is the type of option (e.g. requires a value, can be passed more than once, etc.)
                InputOption::VALUE_NONE,
                // the option description displayed when showing the command help
                'Used to process command without persisting in DB',
                // the default value of the option (for those which allow to pass values)
                null
            )
        ;
        $this->addOption('limit', null, InputOption::VALUE_REQUIRED,
            'How many track to consider before stop',
            0
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln(sprintf('Start analyzing mediaLib directory: %s', $this->medialibFolder));

        $outputSectionParsing = $output->section();
        $dirParser = new MediaLibDirectoryParser(
            $this->medialibFolder,
            $this->pathSeparator,
            array('mp3'),
            $outputSectionParsing
        );
        $candidatesCount = $dirParser->getFilesCount();
        $output->writeln(sprintf('Done with %d candidates files', $candidatesCount));

        $limit = $input->getOption('limit');

        $output->writeln('Checking metatags...');
        $outputSectionMetadata = $output->section();
        $allFiles = $dirParser->getAllFiles();
        $fileIndex = 0;
        foreach ($allFiles as $folder => $filesInFolder) {
            foreach ($filesInFolder as $fullPathToFile) {
                $fileIndex++;
                $percent = round(($fileIndex / $candidatesCount) * 100);
                $fileRelPath = str_replace($this->medialibFolder, '', $fullPathToFile);
                $outputSectionMetadata->overwrite($percent . '%');
                $audioInfo = new Mp3Info($fullPathToFile, true);
                $this->addTrackToCandidate($fileRelPath, $audioInfo);
                if ($limit > 0 && $fileIndex >= $limit) {
                    break;
                }
            }
            if ($limit > 0 && $fileIndex >= $limit) {
                break;
            }
        }
        $outputSectionMetadata->clear();
        if ($limit > 0) {
            $output->writeln(sprintf('Reached limit of %d files', $limit));
        }

        $output->writeln('Load persisted entities...');
        $outputSectionPersisted = $output->section();
        //try to match with existing entities
        $outputSectionPersisted->overwrite('...Artists');
        $artistRepository = new ArtistRepository($this->managerRegistry);
        $this->persistedArtists = $artistRepository->loadPersistedEntities($this->candidateArtist);
        $outputSectionPersisted->overwrite('...Albums');
        $albumRepository = new AlbumRepository($this->managerRegistry);
        $this->persistedAlbums = $albumRepository->loadPersistedEntities($this->candidateAlbums);
        $outputSectionPersisted->overwrite('...Tracks');
        $trackRepository = new TrackRepository($this->managerRegistry);
        $this->persistedTracks = $trackRepository->loadPersistedEntities($this->candidateTracks, true);
        $outputSectionPersisted->clear();

        $output->writeln('Checking for new data...');
        $outputSectionEntities = $output->section();
        $outputSectionEntitiesProgress = $output->section();
        $entitiesToStore = array();
        $fileIndex = 0;
        foreach ($this->candidatesTree as $artistName => $artistAlbums) {
            $artist = $this->persistedArtists[$artistName];
            if (!$artist) {
                $artist = new Artist($artistName);
                $entitiesToStore[] = $artist;
                $this->persistedArtists[$artistName] = $artist;
                $outputSectionEntities->writeln(sprintf('   Artist \'%s\' to be created', $artistName));
            }
            foreach ($artistAlbums as $albumName => $albumsTracks) {
                $album = $this->persistedAlbums[$albumName];
                if (!$album) {
                    $album = new Album($albumName);
                    $entitiesToStore[] = $album;
                    $this->persistedAlbums[$albumName] = $album;
                    $outputSectionEntities->writeln(sprintf('   Album \'%s\' to be created', $albumName));
                }
                foreach ($albumsTracks as $filepath => $metadata) {
                    /** @var Mp3Info $metadata */
                    $fileIndex++;
                    $percent = round(($fileIndex / $candidatesCount) * 100);
                    $outputSectionEntitiesProgress->overwrite($percent . '%');
                    $track = $this->persistedTracks[$filepath];
                    if (!$track) {
                        $track = new Track($metadata->tags['song'] ?? '');
                        $track
                            ->setArtist($artist)
                            ->setAlbum($album)
                            ->setFilepath($filepath)
                            ->setTrackNumber($metadata->tags['track'] ?? null)
                            ->setYear($metadata->tags['year'] ?? null)
                        ;
                        $entitiesToStore[] = $track;
                        $this->persistedTracks[$filepath] = $track;
                        $outputSectionEntities->writeln(sprintf('   Track to be created from %s', $filepath));
                    }
                }
            }
            $outputSectionEntitiesProgress->clear();

            $output->writeln(sprintf('%d new entities detected, store in DB', count($entitiesToStore)));
            if ($input->getOption('no-db')) {
                $output->writeln('Running as a test, no DB storage');
            } else {
                $this->entityRegistry->saveObjectList($entitiesToStore);
            }
            $output->writeln('Import done!');

        }

        return Command::SUCCESS;
    }


    protected function addArtistToCandidate(string $artist)
    {
        if (!isset($this->candidatesTree[$artist])) {
            $this->candidateArtist[] = $artist;
            $this->candidatesTree[$artist] = array();
        }
    }

    protected function addAlbumToCandidate(string $artist, string $album)
    {
        $this->addArtistToCandidate($artist);
        if (!isset($this->candidatesTree[$artist][$album])) {
            $this->candidateAlbums[] = $album;
            $this->candidatesTree[$artist][$album] = array();
        }
    }

    protected function addTrackToCandidate(string $filepath, Mp3Info $metadata)
    {
        $artist = $metadata->tags['artist'] ?? '';
        $album = $metadata->tags['album'] ?? '';
        $this->addAlbumToCandidate($artist, $album);
        if (!isset($this->candidatesTree[$artist][$album][$filepath])) {
            $this->candidateTracks[] = $filepath;
            $this->candidatesTree[$artist][$album][$filepath] = $metadata;
        }
    }


}
