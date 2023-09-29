<?php

namespace App\Controller\API;

use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Keiwen\Cacofony\Route\Annotation\Get;
use Nelmio\ApiDocBundle\Annotation\Model;
use App\Entity\Artist;
use App\Entity\Track;
use App\Entity\Album;
use Keiwen\Cacofony\Http\Request;
use Keiwen\Utils\Sanitize\StringSanitizer;
use App\Repository\AlbumRepository;
use App\Repository\ArtistRepository;
use App\Repository\TrackRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Route("/api", name="api_")
 * @OA\Tag(name="Main")
 */
class GlobalAPIController extends APIController
{



    /**
     * @Get ("/search", name="search")
     * @OA\Get (
     *     summary="Search for track, artist or album",
     *     description="Search for names among track, artist, album",
     *     @OA\Parameter (
     *          name="q",
     *          in="query",
     *          description="name to search for",
     *          @OA\Schema(type="string")
     *     ),
     *     @OA\Response (
     *          response=200,
     *          description="List of results",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="tracks",
     *                  type="array",
     *                  @OA\Items(ref=@Model(type=Track::class, groups={"track"}))
     *              ),
     *              @OA\Property(
     *                  property="artists",
     *                  type="array",
     *                  @OA\Items(ref=@Model(type=Artist::class, groups={"artist"}))
     *              ),
     *              @OA\Property(
     *                  property="albums",
     *                  type="array",
     *                  @OA\Items(ref=@Model(type=Album::class, groups={"album"}))
     *              )
     *          )
     *     )
     * )
     */
    public function search(Request $request, TrackRepository $trackRepository, ArtistRepository $artistRepository, AlbumRepository $albumRepository): JsonResponse
    {
        $search = $request->getRequestParam('q', StringSanitizer::FILTER_ALPHA, '');


        $artistObjects = $artistRepository->searchForName($search);
        $artists = array();
        foreach ($artistObjects as $artistObject) {
            $artist = $artistObject->toArray();
            $this->addApiLink(
                $artist,
                'api_artist_get',
                array('id' => $artistObject->getId()),
            );
            $artists[] = $artist;
        }

        $albumObjects = $albumRepository->searchForName($search);
        $albums = array();
        foreach ($albumObjects as $albumObject) {
            $album = $albumObject->toArray();
            $this->addApiLink(
                $album,
                'api_album_get',
                array('id' => $albumObject->getId()),
            );
            $albums[] = $album;
        }

        $trackObjects = $trackRepository->searchForName($search);
        $tracks = array();
        foreach ($trackObjects as $trackObject) {
            $track = $trackObject->toArray();
            $this->addApiLink(
                $track,
                'api_track_get',
                array('id' => $trackObject->getId()),
            );
            $tracks[] = $track;
        }

        return $this->renderJson(array(
            'tracks' => $tracks,
            'artists' => $artists,
            'albums' => $albums,
        ));
    }



}
