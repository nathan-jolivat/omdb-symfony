<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @Route("/index", name="index")
     */
    public function index()
    {
        getenv('OMDB_API_KEY');
        
        return $this->render('product/index.html.twig');
    }
}
