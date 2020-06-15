<?php

namespace Tasklist\Common\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class HomepageController extends AbstractController
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('Common/homepage/index.html.twig');
    }
}
