<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Vote;


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
            'omdbApiKey' => $omdbApiKey,
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

        return $this->render('omdb/multiple.html.twig', [
            'omdbApiKey' => $omdbApiKey
        ]);
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

        if ( isset( $response->Search ))
        {
            //Rendu de la liste des films
            return $this->render('omdb/multiple.html.twig', [
                'responseMovie' => $response,
                'omdbApiKey' => $omdbApiKey,
                'filmFound' => true,
                // Paramètre attendu par la route :
                'requestedTitle' => $requestedTitle
            ]);
        }else{
            return $this->render('omdb/multiple.html.twig', [
                'requestedTitle' => $requestedTitle,
                'omdbApiKey' => $omdbApiKey,
                'filmFound' => false
            ]);
        }
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
        curl_setopt($ch, CURLOPT_URL, "http://www.omdbapi.com/?apikey=" . $omdbApiKey . "&i=" . $imdbId);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result_curl = curl_exec($ch);
        $idResponse = json_decode($result_curl);

        // Récupération de la moyenne des notes
        $moyenneNotes = $this->getDoctrine()
            ->getRepository(Vote::class)
            ->findAverageRating( $imdbId );


        return $this->render('omdb/specific.html.twig',
            [
                'omdbApiKey' => $omdbApiKey,
                'requestedId' => $imdbId,
                'responseMovie' => $idResponse,
                'avgRating' => $moyenneNotes
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
    // On fait appel à l'objet Request appelé en haut dans les use machin
    {
        $omdbApiKey = getenv('OMDB_API_KEY');
        $requestedId = $request->query->get('movieImdbID');

        $makeUrl = "http://www.omdbapi.com/?apikey=" . $omdbApiKey . "&i=" . urlencode($requestedId);
        $query = file_get_contents($makeUrl);
        $idResponse = json_decode($query);


        return $this->render('omdb/specific.html.twig', [
            'omdbApiKey' => $omdbApiKey,
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


    /**
     * Go to the Contact page
     *
     * * @Route("/contact", name="contact")
     *
     */
    public function contactPage()
    {
        $omdbApiKey = getenv('OMDB_API_KEY');
        return $this->render('omdb/contact.html.twig', [
            'omdbApiKey' => $omdbApiKey,
        ]);
    }

    /**
     * Send film details by email
     *
     * @Route("/share", name="share")
     */
    public function sendByMail( Request $request, \Swift_Mailer $mailer )
    {
        // dump($request->request->all() );
        $imdbId = $request->request->get('movieId');
        $mailRecipient = $request->request->get('mailAdress');

        $omdbApiKey = getenv('OMDB_API_KEY');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://www.omdbapi.com/?apikey=" . $omdbApiKey . "&i=" . $imdbId);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result_curl = curl_exec($ch);
        $idResponse = json_decode($result_curl);

        // On teste que les données récupérées sont bien un film
        if ( isset( $idResponse->Response ) && ($idResponse->Response == "True"))
        {

            /* // Affichage de la sortie pour DEV
            return $this->render('mails/fichefilm.html.twig',
            [
                'movie' => $idResponse
            ]);  */

            // On stocke notre sortie dans une variable
            $output = $this->render('mails/fichefilm.html.twig',
                [
                    'movie' => $idResponse
                ]);

            // On prépare notre message avec un objet SwiftMailer
            $mail = (new \Swift_Message('Hey gros, mate donc ce film !'))
                ->setFrom(['lalicornemagique@youpi.com' => 'Dieu'])
                ->setTo($mailRecipient)
                ->setBody(
                    $output,
                    'text/html'
                );

            // Appel du facteur et envoi du mail
            $mailer->send( $mail );

            // Message de confirmation qui flashouille
            $this->addFlash(
                'success',
                'Message bien envoyé, gros. ;)'
            );

            // Redirection vers la fiche détaillée
            return $this->redirectToRoute('find-specific-id', [
                'imdbId' => $imdbId
            ]);

        }else {

            return $this->render('omdb/not_found.html.twig', [
                'requestedTitle' => $idResponse->Title,
                'omdbApiKey' => $omdbApiKey,
            ]);
        }

    }

    /**
     * Send a note for a film méthodeGET
     *
     * @Route( "/nouveauvote/{imdbID}/{note}", name="nouveauvote" )
     * @param $imdbID
     * @param $note
     *
     * Cette route ne sert plus à rien on peut la jeter
     */
    public function sendNote($imdbID, $note) {

        // On créée une nouvelle entité de classe Vote
        $vote = new Vote();
        // On lui donne les valeurs de notre Vote, la note et le film en question
        $vote->setImdbID( $imdbID );
        $vote->setNote( $note );

        // On fait appel au gestionnaire d'entités inclus avec Doctrine
        $entityManager = $this->getDoctrine()->getManager();

        // On demande au gestionnaire de PERSISTER les données en base
        // La méthode persist génère les requêtes SQL d'insertion en base
        $entityManager->persist( $vote );

        // Avec la méthode flush, les requêtes générées partent en base d'un coup.
        $entityManager->flush();

        // Message de confirmation
        $this->addFlash(
            'success',
            'Merci d\'avoir voté !'
        );

        return $this->redirectToRoute('find-specific-id', [
            'imdbId' => $imdbID
        ]);

    }

    /**
     * Catch the note for the film POST route
     *
     * @Route( "/nouveauVotePOST", name="nouveauVotePOST" )
     * @param Request $request
     *
     */
    public function catchNote( Request $request )
    {
        $note = $request->request->get('note');
        $imdbID = $request->request->get('movieId');

        $vote = new Vote();
        $vote->setImdbID( $imdbID );
        $vote->setNote( $note );

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist( $vote );
        $entityManager->flush();
        $this->addFlash(
            'success',
            'Merci d\'avoir voté !'
        );

        return $this->redirectToRoute('find-specific-id', [
            'imdbId' => $imdbID
        ]);
    }



}