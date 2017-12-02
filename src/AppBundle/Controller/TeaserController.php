<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Teaser;

class TeaserController extends Controller
{
    /**
     * @return Response
     */
    public function blockAction()
    {
        $teaserList = $this->getDoctrine()->getManager()
            ->getRepository(Teaser::class)->findBy(['active' => true], ['sort' => 'asc']);

        return $this->render('@App/Teaser/block.html.twig', array(
            'teaserList' => $teaserList
        ));
    }
}