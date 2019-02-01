<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MainPageController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function homepage()
    {
    	$calendars = null;

        return $this->render('main_page/homepage.html.twig', [
        	'calendars'		=> $calendars,
        ]);
    }
}
