<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OMDBController extends AbstractController
{
    /**
     * Redirect to multiple view if there are no GET parameters
     *
     * @Route("/specific", name="specific")
     */
    public function specific()
    {
        return $this->redirectToRoute('multiple');
    }

    /**
     * Show details of a specific movie
     *
     * @Route("/find-specific/{title}", name="find-specific")
     * @param $title
     *
     * @return Response
     */
    public function getMovieTitle($title)
    {
        $omdbApiKey = getenv('OMDB_API_KEY');

        $makeTitleUrl = "http://www.omdbapi.com/?apikey=" . $omdbApiKey . "&t=" . urlencode($title);
        $queryTitle = file_get_contents($makeTitleUrl);

        $titleResponse = json_decode($queryTitle);

        return $this->render('omdb/specific.html.twig', [
            'responseMovie' => $titleResponse,
            'omdbApiKey'    => $omdbApiKey,
            'titleResponse' => $titleResponse !== null ? $titleResponse : null
        ]);
    }

    /**
     * Multiple items search view without GET parameters
     *
     * @Route("/", name="multiple")
     */
    public function multiple()
    {
        $omdbApiKey = getenv('OMDB_API_KEY');

        return $this->render('omdb/multiple.html.twig', ['omdbApiKey' => $omdbApiKey]);
    }

    /**
     * Find by key word movies result
     *
     * @Route("/find-multiple", name="find-multiple")
     * @param Request $request
     *
     * @return Response
     */
    public function getMovieTitleMultiple(Request $request)
    {
        $omdbApiKey = getenv('OMDB_API_KEY');

        $requestedTitle = $request->query->get('movieTitle');

        $makeUrl = "http://www.omdbapi.com/?apikey=" . $omdbApiKey . "&s=" . urlencode($requestedTitle);

        $query = file_get_contents($makeUrl);

        $response = json_decode($query);

        return $this->render('omdb/multiple.html.twig', [
            'responseMovie' => $response,
            'omdbApiKey'    => $omdbApiKey,
            'requestedTitle' => $requestedTitle
        ]);
    }



    /**
     * Gives OMDB API Key to Navbar view
     *
     * @return Response
     */
    public function givesApiKeyToView()
    {
        $omdbApiKey = getenv('OMDB_API_KEY');

        return $this->render('omdb/layout/navbar.html.twig', ['omdbApiKey' => $omdbApiKey]);
    }
}
