<?php

namespace App\Controller;

use App\Controller\MailController;
use Swift_Mailer;
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

        $makeUrl = "http://www.omdbapi.com/?apikey=" . getenv('OMDB_API_KEY') . "&s=" . urlencode($requestedTitle);

        $query = file_get_contents($makeUrl);

        $response = json_decode($query);

        return $this->render('omdb/multiple.html.twig', [
            'responseMovie'  => $response,
            'omdbApiKey'     => $omdbApiKey,
            'requestedTitle' => $requestedTitle
        ]);
    }

    /**
     * Send movie informations to a recipient by mail
     *
     * @Route("/prepare-to-send-mail", name="prepare-to-send-mail")
     *
     * @param Request      $request
     *
     * @param Swift_Mailer $mailer
     *
     * @return Response
     */
    public function shareMovie(Request $request, Swift_Mailer $mailer)
    {
        $recipient = $request->request->get('nameRecipient');

        $movieDetails = [
            'Title'    => $request->request->get('movieTitle'),
            'Poster'   => $request->request->get('moviePoster'),
            'Year'     => $request->request->get('movieYear'),
            'Released' => $request->request->get('movieReleased'),
            'Actors'   => $request->request->get('movieActors'),
            'Plot'     => $request->request->get('moviePlot'),
            'Website'  => $request->request->get('movieWebsite'),
            'Genre'    => $request->request->get('movieGenre')
        ];

        $message = (new \Swift_Message("Partage d'un film"))
            ->setFrom('send@example.com')
            ->setTo($request->request->get('emailRecipient'))
            ->setBody($this->renderView(
                'emails/template.html.twig',
                [
                    'name'         => $recipient,
                    'movieDetails' => $movieDetails
                ]
            ),
            'text/html'
        );

        $mailer->send($message);

        return $this->forward('App\Controller\OMDBController::GetMailSentView',
            [
                'movieTitle' => $request->request->get('movieTitle')
            ]
        );
    }

    /**
     * Get success view when share mail is sent
     *
     * @Route("/mail-sent", name="mail-sent")
     * @param $movieTitle
     *
     * @return Response
     */
    public function getMailSentView($movieTitle)
    {
        $omdbApiKey = getenv('OMDB_API_KEY');

        return $this->render('omdb/mail-sent.html.twig', [
            'omdbApiKey' => $omdbApiKey,
            'movieTitle' => $movieTitle
        ]);
    }

    /**
     * Gives OMDB API Key to Navbar view
     *
     * @return Response
     */
    private function givesApiKeyToView()
    {
        $omdbApiKey = getenv('OMDB_API_KEY');

        return $this->render('omdb/layout/navbar.html.twig', ['omdbApiKey' => $omdbApiKey]);
    }
}
