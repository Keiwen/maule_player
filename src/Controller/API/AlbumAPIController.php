<?php

namespace App\Controller\API;

use App\Entity\Album;
use App\Repository\AlbumRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Keiwen\Cacofony\Route\Annotation\Get;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;

/**
 * @Route("/api/album", name="api_album_")
 * @OA\Tag(name="album")
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
        $albumObjects = $albumRepository->findAll();
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


        return $this->renderJson($jsonReturn);
    }



}
