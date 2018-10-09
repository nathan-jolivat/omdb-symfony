<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\VarDumper\VarDumper;

class ProductController extends AbstractController
{
    /**
     * @Route("/index", name="index")
     */
    public function index()
    {
        $omdbApiKey = getenv('OMDB_API_KEY');
        $apiResponse = file_get_contents('http://www.omdbapi.com/?i=tt3896198&apikey=35510d96');
        $jsonResponse = json_decode($apiResponse);

        return $this->render('omdb/index.html.twig', [ 'omdbApiKey' => $omdbApiKey, 'jsonResponse' => $jsonResponse ]);
    }

    /**
     * @Route("/find", name="find")
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getMovieTitle(Request $request)
    {
        $omdbApiKey = getenv('OMDB_API_KEY');

        $requestedTitle = $request->query->get('movieTitle');

        $makeUrl = "http://www.omdbapi.com/?apikey=" . getenv('OMDB_API_KEY') . "&t=". urlencode($requestedTitle);

        $query = file_get_contents($makeUrl);

        $response = json_decode($query);

        return $this->render('omdb/index.html.twig', ['responseMovie' => $response, 'omdbApiKey' => $omdbApiKey]);
    }

    public function givesApiKeyToView()
    {
        $omdbApiKey = getenv('OMDB_API_KEY');

        return $this->render('omdb/layout/navbar.html.twig', [ 'omdbApiKey' => $omdbApiKey ]);
    }
}
