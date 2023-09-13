<?php

namespace App\Controller\API;

use App\Entity\Artist;
use App\Repository\ArtistRepository;
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
        $artistObjects = $artistRepository->findAll();
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


        return $this->renderJson($jsonReturn);
    }



}
