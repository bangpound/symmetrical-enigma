<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class EnvDumpController extends Controller
{
    /**
     * @Route("/env/dump", name="env_dump")
     */
    public function index()
    {
        return $this->render('env_dump/index.html.twig', [
            'env' => $_ENV,
        ]);
    }
}
