<?php

namespace App\Controller\API;

use App\Entity\Track;
use App\Repository\TrackRepository;
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
    public function list(TrackRepository $trackRepository): JsonResponse
    {
        $trackObjects = $trackRepository->findAll();
        $tracks = array();
        foreach ($trackObjects as $trackObject) {
            $track = $trackObject->toArray();
            $this->addApiLink(
                $track,
                'api_track_get',
                array('id' => $trackObject->getId()),
            );
            $track['filepath'] = str_replace('\\', '', $track['filepath']);
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
