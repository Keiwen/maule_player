<?php

namespace App\Controller\API;

use App\Entity\Artist;
use App\Entity\Track;
use App\Entity\Album;
use App\Repository\AlbumRepository;
use App\Repository\ArtistRepository;
use App\Repository\TrackRepository;
use Keiwen\Cacofony\Http\Request;
use Keiwen\Utils\Sanitize\StringSanitizer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Keiwen\Cacofony\Route\Annotation\Get;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;

/**
 * @Route("/api/artist", name="api_artist_")
 * @OA\Tag(name="Artist")
 */
class ArtistAPIController extends APIController
{


    /**
     * @Get ("/", name="list")
     * @OA\Get (
     *     summary="Get artists",
     *     description="Get all artists informations",
     *     @OA\Response (
     *          response=200,
     *          description="List of artists",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="artists",
     *                  type="array",
     *                  @OA\Items(ref=@Model(type=Artist::class, groups={"artist"}))
     *              )
     *          )
     *     )
     * )
     */
    public function list(ArtistRepository $artistRepository): JsonResponse
    {
        $artistObjects = $artistRepository->findBy([], ['name' => 'ASC']);
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

        return $this->renderJson(array('artists' => $artists));
    }



    /**
     * @Get ("/{id}", name="get")
     * @OA\Get (
     *     summary="Get artist",
     *     description="Get artist informations",
     *     @OA\Parameter (
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="Artist ID",
     *          example="1",
     *          @OA\Schema(type="integer")
     *     ),
     *     @OA\Response (
     *          response=200,
     *          description="artist",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="artist",
     *                  ref=@Model(type=Artist::class, groups={"artist"})
     *              )
     *          )
     *     ),
     *     @OA\Response (
     *          response=404,
     *          description="Cannot find artist"
     *     )
     * )
     */
    public function artist(Artist $artist): JsonResponse
    {
        $artistData = $artist->toArray();
        $jsonReturn = array(
            'artist' => $artistData
        );
        $this->addApiLink(
            $jsonReturn,
            'api_artist_list',
            array(),
            'collection'
        );
        $this->addApiLink(
            $jsonReturn,
            'api_artist_tracks',
            array('id' => $artist->getId()),
            'section'
        );
        $this->addApiLink(
            $jsonReturn,
            'api_artist_albums',
            array('id' => $artist->getId()),
            'section'
        );


        return $this->renderJson($jsonReturn);
    }


    /**
     * @Get ("/{id}/tracks", name="tracks")
     * @OA\Get (
     *     summary="Get artist tracks",
     *     description="Get all tracks from artist",
     *     @OA\Parameter (
     *          name="id",
     *          in="path",
     *          description="Artist ID",
     *          required=true,
     *          @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter (
     *          name="orderBy",
     *          in="query",
     *          description="Choose tracks order.
     *                      Leave it empty for the most recent first.
     *                      Use 'name' for alphabetical order.
     *                      Use 'oldest' to reverse default order.
     *                      use 'importDate' to get most recently imported first.
     *          ",
     *          @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter (
     *          name="limit",
     *          in="query",
     *          description="Maximum number of tracks returned. 0 or empty to get all tracks.",
     *          example="10",
     *          @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter (
     *          name="offset",
     *          in="query",
     *          description="Displacement from the first matching result",
     *          example="0",
     *          @OA\Schema(type="integer")
     *     ),
     *     @OA\Response (
     *          response=200,
     *          description="List of tracks",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="tracks",
     *                  type="array",
     *                  @OA\Items(ref=@Model(type=Track::class, groups={"track"}))
     *              )
     *          )
     *     )
     * )
     */
    public function tracks(Artist $artist, Request $request, TrackRepository $trackRepository): JsonResponse
    {
        $orderBy = $request->getRequestParam('orderBy', StringSanitizer::FILTER_ALPHA, '');
        $limit = $request->getRequestParam('limit', StringSanitizer::FILTER_INT, 0);
        $offset = $request->getRequestParam('offset', StringSanitizer::FILTER_INT, 0);

        $trackObjects = $trackRepository->findByArtist($artist, $orderBy, $limit, $offset);

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
        $jsonReturn = array(
            'tracks' => $tracks
        );
        $this->addApiLink(
            $jsonReturn,
            'api_artist_get',
            array('id' => $artist->getId()),
        );

        return $this->renderJson($jsonReturn);
    }


    /**
     * @Get ("/{id}/albums", name="albums")
     * @OA\Get (
     *     summary="Get artist albums",
     *     description="Get all albums where artist is included",
     *     @OA\Parameter (
     *          name="id",
     *          in="path",
     *          description="Artist ID",
     *          required=true,
     *          @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter (
     *          name="orderBy",
     *          in="query",
     *          description="Choose albums order.
     *                      Leave it empty for the most recent first.
     *                      Use 'name' for alphabetical order.
     *                      Use 'oldest' to reverse default order.
     *                      use 'importDate' to get most recently imported first.
     *          ",
     *          @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter (
     *          name="limit",
     *          in="query",
     *          description="Maximum number of album returned. 0 or empty to get all albums.",
     *          example="10",
     *          @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter (
     *          name="offset",
     *          in="query",
     *          description="Displacement from the first matching result",
     *          example="0",
     *          @OA\Schema(type="integer")
     *     ),
     *     @OA\Response (
     *          response=200,
     *          description="List of albums",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="albums",
     *                  type="array",
     *                  @OA\Items(ref=@Model(type=Album::class, groups={"album"}))
     *              )
     *          )
     *     )
     * )
     */
    public function albums(Artist $artist, Request $request, AlbumRepository $albumRepository): JsonResponse
    {
        $orderBy = $request->getRequestParam('orderBy', StringSanitizer::FILTER_ALPHA, '');
        $limit = $request->getRequestParam('limit', StringSanitizer::FILTER_INT, 0);
        $offset = $request->getRequestParam('offset', StringSanitizer::FILTER_INT, 0);

        $albumObjects = $albumRepository->searchByArtist($artist, $orderBy, $limit, $offset);

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
        $jsonReturn = array(
            'albums' => $albums
        );
        $this->addApiLink(
            $jsonReturn,
            'api_artist_get',
            array('id' => $artist->getId()),
        );

        return $this->renderJson($jsonReturn);
    }


}
