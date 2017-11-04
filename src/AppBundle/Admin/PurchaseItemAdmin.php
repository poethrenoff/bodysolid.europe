<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use AppBundle\Entity\Purchase;
use AppBundle\Entity\Product;

class PurchaseItemAdmin extends AbstractAdmin
{
    /**
     * {@inheritdoc}
     */
    protected $parentAssociationMapping = 'purchase';

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('purchase', EntityType::class, [
                'class' => Purchase::class,
                'label' => 'Производитель',
            ])
            ->add('product', EntityType::class, [
                'class' => Product::class,
                'label' => 'Производитель',
            ])
            ->add('price', NumberType::class, ['label' => 'Email'])
            ->add('quantity', IntegerType::class, ['label' => 'Телефон']);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id', null, ['label' => 'ID'])
            ->addIdentifier('purchase', null, ['label' => 'Заказ'])
            ->add('product', null, ['label' => 'Товар'])
            ->add('price', null, ['label' => 'Цена', 'editable' => true])
            ->add('quantity', null, ['label' => 'Количество', 'editable' => true])
            ->add('_action', 'actions', [
                'label' => 'Операции',
                'actions' => [
                    'edit' => [],
                    'delete' => []
                ]]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('create');
    }
}