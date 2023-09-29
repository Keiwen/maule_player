<?php

namespace App\Controller\API;

use App\Entity\Album;
use App\Entity\Track;
use App\Repository\AlbumRepository;
use App\Repository\TrackRepository;
use Keiwen\Cacofony\Http\Request;
use Keiwen\Utils\Sanitize\StringSanitizer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Keiwen\Cacofony\Route\Annotation\Get;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;

/**
 * @Route("/api/album", name="api_album_")
 * @OA\Tag(name="Album")
 */
class AlbumAPIController extends APIController
{


    /**
     * @Get ("/", name="list")
     * @OA\Get (
     *     summary="Get albums",
     *     description="Get all albums informations",
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
    public function list(AlbumRepository $albumRepository): JsonResponse
    {
        $albumObjects = $albumRepository->findBy([], ['name' => 'ASC']);
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

        return $this->renderJson(array('albums' => $albums));
    }



    /**
     * @Get ("/{id}", name="get")
     * @OA\Get (
     *     summary="Get album",
     *     description="Get album informations",
     *     @OA\Parameter (
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="album ID",
     *          example="1",
     *          @OA\Schema(type="integer")
     *     ),
     *     @OA\Response (
     *          response=200,
     *          description="album",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="album",
     *                  ref=@Model(type=Album::class, groups={"album"})
     *              )
     *          )
     *     ),
     *     @OA\Response (
     *          response=404,
     *          description="Cannot find album"
     *     )
     * )
     */
    public function album(Album $album): JsonResponse
    {
        $albumData = $album->toArray();
        $jsonReturn = array(
            'album' => $albumData
        );
        $this->addApiLink(
            $jsonReturn,
            'api_album_list',
            array(),
            'collection'
        );
        $this->addApiLink(
            $jsonReturn,
            'api_album_tracks',
            array('id' => $album->getId()),
            'section'
        );


        return $this->renderJson($jsonReturn);
    }



    /**
     * @Get ("/{id}/tracks", name="tracks")
     * @OA\Get (
     *     summary="Get album tracks",
     *     description="Get all tracks from album",
     *     @OA\Parameter (
     *          name="id",
     *          in="path",
     *          description="Album ID",
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
    public function tracks(Album $album, Request $request, TrackRepository $trackRepository): JsonResponse
    {
        $orderBy = $request->getRequestParam('orderBy', StringSanitizer::FILTER_ALPHA, '');
        $limit = $request->getRequestParam('limit', StringSanitizer::FILTER_INT, 0);
        $offset = $request->getRequestParam('offset', StringSanitizer::FILTER_INT, 0);

        $trackObjects = $trackRepository->findByAlbum($album, $orderBy, $limit, $offset);

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
            'api_album_get',
            array('id' => $album->getId()),
        );

        return $this->renderJson($jsonReturn);
    }


}
