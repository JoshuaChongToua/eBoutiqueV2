<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class ErrorController extends AbstractController
{
    /*
     * Renvoie vers la homepage dès qu'une url qui n'existe pas est essayée
     */
    #[Route('/{any}', name: 'not_found_redirect', requirements: ['any' => '.*'])]
    public function redirectToHome(): RedirectResponse
    {
        return $this->redirectToRoute('app_home');
    }
}
