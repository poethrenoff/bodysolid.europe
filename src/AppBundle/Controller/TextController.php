<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\Text;

class TextController extends Controller
{
    /**
     * @Route("/about", name="about")
     * @Route("/delivery", name="delivery")
     * @Route("/contact", name="contact")
     *
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        return $this->render('AppBundle::Text/text.html.twig', array(
            'textItem' => $this->getText($request->get('_route'))
        ));
    }

    /**
     * @param string $name
     * @return Response
     */
    public function blockAction(string $name)
    {
        return $this->render('AppBundle::Text/block.html.twig', array(
            'textItem' => $this->getText($name)
        ));
    }

    /**
     * @throws NotFoundHttpException
     *
     * @param string $name
     * @return string
     */
    protected function getText(string $name)
    {
        $text = $this->getDoctrine()->getManager()
            ->getRepository(Text::class)->findOneByName($name);

        if (!$text) {
            throw new NotFoundHttpException('Страница не найдена');
        }

        return $text;
    }
}