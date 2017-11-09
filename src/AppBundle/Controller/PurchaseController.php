<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Service\Cart;
use AppBundle\Service\Preference;
use AppBundle\Entity\Product;
use AppBundle\Entity\Purchase;
use AppBundle\Entity\PurchaseItem;
use AppBundle\Form\PurchaseType;

class PurchaseController extends Controller
{
    /**
     * @var Cart
     */
    protected $cart;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var Preference
     */
    protected $preference;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * PurchaseController constructor
     *
     * @param Cart $cart
     * @param Session $session
     * @param Preference $preference
     * @param EntityManagerInterface $entityManager
     * @param \Swift_Mailer $mailer
     */
    public function __construct(Cart $cart,
                                Session $session,
                                Preference $preference,
                                EntityManagerInterface $entityManager,
                                \Swift_Mailer $mailer)
    {
        $this->cart = $cart;
        $this->session = $session;
        $this->preference = $preference;
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
    }

    /**
     * @Route("/purchase", name="purchase")
     *
     * @throws \Exception
     *
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $flush = $this->session->getFlashBag();

        foreach ($flush->get('success') as $message) {
            if ($message) {
                return $this->render('AppBundle::Purchase/success.html.twig');
            }
        }

        $productList = array();
        foreach ($this->cart->getItems() as $item ) {
            $productList[$item->id] = $this->getProduct($item->id);
        }

        $purchase = new Purchase();
        $form = $this->createForm(PurchaseType::class, $purchase);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->entityManager->beginTransaction();

            try {
                $purchase->setSum($this->cart->getSum());

                foreach ($this->cart->getItems() as $item ) {
                    $purchaseItem = new PurchaseItem();
                    $purchaseItem
                        ->setPurchase($purchase)
                        ->setProduct($productList[$item->id])
                        ->setPrice($item->price)
                        ->setQuantity($item->quantity);
                    $purchase->addItem($purchaseItem);
                }

                $this->entityManager->persist($purchase);
                $this->entityManager->flush();

                $this->sendMessages($purchase);

                $this->entityManager->commit();
            } catch (\Exception $e) {
                $this->entityManager->rollBack();
                throw $e;
            }

            $this->cart->clear();

            $flush->add('success', true);

            return $this->redirectToRoute('purchase');
        }

        return $this->render('AppBundle::Purchase/form.html.twig', array(
            'productList' => $productList, 'form' => $form->createView(),
        ));
    }

    /**
     * Send messages
     *
     * @param Purchase $purchase
     */
    protected function sendMessages(Purchase $purchase)
    {
        $from_email = $this->preference->get('from_email');
        $from_name = $this->preference->get('from_name');

        $manager_email= $this->preference->get('manager_email');
        $manager_subject = $this->preference->get('manager_subject');
        $client_subject = $this->preference->get('client_subject');

        $manager_message = (new \Swift_Message())
            ->setSubject($manager_subject)
            ->setFrom($from_email, $from_name)
            ->setTo($manager_email)
            ->setBody(
                $this->renderView('AppBundle::Purchase/manager_message.html.twig', array(
                    'purchase' => $purchase
                )),
                'text/html'
            );
        $this->mailer->send($manager_message);

        $client_message = (new \Swift_Message())
            ->setSubject($client_subject)
            ->setFrom($from_email, $from_name)
            ->setTo($purchase->getEmail())
            ->setBody(
                $this->renderView('AppBundle::Purchase/client_message.html.twig', array(
                    'purchase' => $purchase
                )),
                'text/html'
            );
        $this->mailer->send($client_message);
    }

    /**
     * Get product
     *
     * @param int $id
     * @return Product
     */
    protected function getProduct($id)
    {
        $productItem = $this->getDoctrine()->getManager()
            ->getRepository(Product::class)->findOneBy(['id' => $id, 'active' => true]);

        if (!$productItem) {
            throw new NotFoundHttpException('Страница не найдена');
        }

        return $productItem;
    }
}