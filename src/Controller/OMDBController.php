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
     * Display a specific movie by IMDB ID
     *
     * @Route("/find-specific-id/{imdbId}", name="find-specific-id")
     * @param $imdbId
     *
     */
    public function getMovieId($imdbId)
    {
        $omdbApiKey = getenv('OMDB_API_KEY');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://www.omdbapi.com/?apikey=". $omdbApiKey . "&i=" . $imdbId);
        curl_setopt($ch,  CURLOPT_RETURNTRANSFER, true);

        $result_curl = curl_exec($ch);
        $idResponse = json_decode($result_curl);


        return $this->render('omdb/specific.html.twig',
            [
                'omdbApiKey' => $omdbApiKey,
                'requestedId' => $imdbId,
                'responseMovie' => $idResponse,
            ]);

    }

    /**
     * Find a movie by IMDB reference
     *
     * @Route("/myform-id/", name="myform-id")
     * @param Request $request
     *
     * @return Response
     */
     public function getMovieById(Request $request)
    {
        $omdbApiKey = getenv('OMDB_API_KEY');
        $requestedId = $request->query->get('movieImdbID');

        $makeUrl = "http://www.omdbapi.com/?apikey=" . $omdbApiKey . "&i=" . urlencode($requestedId);
        $query = file_get_contents($makeUrl);
        $idResponse = json_decode($query);


        return $this->render('omdb/specific.html.twig', [
            'omdbApiKey'    => $omdbApiKey,
            //'requestedId' => $imdbId,
            'responseMovie' => $idResponse,
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


/**
 * Go to the Contact page
 *
 *
 */

