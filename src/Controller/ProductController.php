<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @Route("/specific", name="specific")
     */
    public function specific()
    {
        $omdbApiKey = getenv('OMDB_API_KEY');

        return $this->render('omdb/specific.html.twig', ['omdbApiKey' => $omdbApiKey]);
    }

    /**
     * @Route("/find-specific/{title}", name="find-specific")
     * @param $title
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getMovieTitle($title)
    {
        $omdbApiKey = getenv('OMDB_API_KEY');

        $makeTitleUrl = "http://www.omdbapi.com/?apikey=" . getenv('OMDB_API_KEY') . "&t=" . urlencode($title);
        $queryTitle = file_get_contents($makeTitleUrl);

        $titleResponse = json_decode($queryTitle);

        return $this->render('omdb/specific.html.twig', [
            'responseMovie' => $titleResponse,
            'omdbApiKey'    => $omdbApiKey,
            'titleResponse' => $titleResponse !== null ? $titleResponse : null
        ]);
    }

    /**
     * @Route("/multiple", name="multiple")
     */
    public function multiple()
    {
        $omdbApiKey = getenv('OMDB_API_KEY');

        return $this->render('omdb/multiple.html.twig', ['omdbApiKey' => $omdbApiKey]);
    }

    /**
     * @Route("/find-multiple", name="find-multiple")
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getMovieTitleMultiple(Request $request)
    {
        $omdbApiKey = getenv('OMDB_API_KEY');

        $requestedTitle = $request->query->get('movieTitle');

        $makeUrl = "http://www.omdbapi.com/?apikey=" . getenv('OMDB_API_KEY') . "&s=" . urlencode($requestedTitle);

        $query = file_get_contents($makeUrl);

        $response = json_decode($query);

        return $this->render('omdb/multiple.html.twig', [
            'responseMovie' => $response,
            'omdbApiKey'    => $omdbApiKey
        ]);
    }

    public function givesApiKeyToView()
    {
        $omdbApiKey = getenv('OMDB_API_KEY');

        return $this->render('omdb/layout/navbar.html.twig', ['omdbApiKey' => $omdbApiKey]);
    }
}
