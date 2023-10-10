<?php

namespace App\Controller\API;

use App\Entity\Track;
use App\Repository\TrackRepository;
use Keiwen\Cacofony\Http\Request;
use Keiwen\Utils\Sanitize\StringSanitizer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Keiwen\Cacofony\Route\Annotation\Get;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;

/**
 * @Route("/api/track", name="api_track_")
 * @OA\Tag(name="Track")
 */
class TrackAPIController extends APIController
{


    /**
     * @Get ("/", name="list")
     * @OA\Get (
     *     summary="Get tracks",
     *     description="Get all tracks informations",
     *     @OA\Parameter (
     *          name="limit",
     *          in="query",
     *          description="Maximum number of tracks returned. 0 or empty to get all tracks.",
     *          example="0",
     *          @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter (
     *          name="randomize",
     *          in="query",
     *          description="",
     *          example="false",
     *          @OA\Schema(type="boolean")
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
    public function list(TrackRepository $trackRepository, Request $request): JsonResponse
    {
        $limit = $request->getRequestParam('limit', StringSanitizer::FILTER_INT, 0);
        $randomize = $request->getRequestParam('randomize', StringSanitizer::FILTER_BOOLEAN, false);
        $trackObjects = $trackRepository->findAllFullTrack($randomize ? 0 : $limit);
        if ($randomize) {
            shuffle($trackObjects);
            $trackObjects = array_slice($trackObjects, 0, $limit);
        }
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

        return $this->renderJson(array('tracks' => $tracks));
    }



    /**
     * @Get ("/{id}", name="get")
     * @OA\Get (
     *     summary="Get track",
     *     description="Get track informations",
     *     @OA\Parameter (
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="Track ID",
     *          example="1",
     *          @OA\Schema(type="integer")
     *     ),
     *     @OA\Response (
     *          response=200,
     *          description="track",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="track",
     *                  ref=@Model(type=Track::class, groups={"track", "trackAndArtist", "trackAndAlbum", "artist", "album"})
     *              )
     *          )
     *     ),
     *     @OA\Response (
     *          response=404,
     *          description="Cannot find track"
     *     )
     * )
     */
    public function track(TrackRepository $trackRepository, int $id): JsonResponse
    {
        $track = $trackRepository->findFullTrack($id);
        if (!$track) {
            throw new NotFoundHttpException('Cannot find track ' . $id);
        }
        $trackData = $track->toArray();
        $this->addApiLink(
            $trackData['artist'],
            'api_artist_get',
            array('id' => $track->getArtist()->getId()),
        );
        $this->addApiLink(
            $trackData['album'],
            'api_album_get',
            array('id' => $track->getAlbum()->getId()),
        );
        $jsonReturn = array(
            'track' => $trackData
        );
        $this->addApiLink(
            $jsonReturn,
            'api_track_list',
            array(),
            'collection'
        );

        return $this->renderJson($jsonReturn);
    }



}
