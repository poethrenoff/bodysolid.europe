<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use AppBundle\Service\Preference;
use AppBundle\Form\CallbackType;

class CallbackController extends Controller
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * @var Preference
     */
    protected $preference;

    /**
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * CallbackController constructor
     *
     * @param Session $session
     * @param Preference $preference
     * @param \Swift_Mailer $mailer
     */
    public function __construct(Session $session,
                                Preference $preference,
                                \Swift_Mailer $mailer)
    {
        $this->session = $session;
        $this->preference = $preference;
        $this->mailer = $mailer;
    }
    
    /**
     * @Route("/callback", name="callback")
     *
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $flush = $this->session->getFlashBag();

        foreach ($flush->get('success') as $message) {
            if ($message) {
                return $this->render('AppBundle::Callback/success.html.twig');
            }
        }

        $form = $this->createForm(CallbackType::class, null, [
            'action' => $this->generateUrl('callback')
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $from_email = $this->preference->get('from_email');
            $from_name = $this->preference->get('from_name');
            $callback_email = $this->preference->get('callback_email');
            $callback_subject = $this->preference->get('callback_subject');

            $message = (new \Swift_Message())
                ->setSubject($callback_subject)
                ->setFrom($from_email, $from_name)
                ->setTo($callback_email)
                ->setBody(
                    $this->renderView('AppBundle::Callback/message.html.twig', array(
                        'callback' => $form->getData(),
                    )),
                    'text/html'
                );
            $this->get('mailer')->send($message);

            $flush->add('success', true);

            return $this->redirectToRoute('callback');
        }

        return $this->render('AppBundle::Callback/form.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}