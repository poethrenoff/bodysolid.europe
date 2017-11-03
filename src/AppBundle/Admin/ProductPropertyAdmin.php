<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use AppBundle\Entity\Product;

class ProductPropertyAdmin extends AbstractAdmin
{
    /**
     * {@inheritdoc}
     */
    protected $parentAssociationMapping = 'product';

    /**
     * {@inheritdoc}
     */
    protected $datagridValues = [
        '_sort_by' => 'sort',
        '_sort_order' => 'asc',
    ];

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('product', EntityType::class, ['class' => Product::class, 'label' => 'Товар'])
            ->add('name', TextType::class, ['label' => 'Имя'])
            ->add('title', TextType::class, ['label' => 'Название'])
            ->add('value', TextType::class, ['label' => 'Значение'])
            ->add('sort', IntegerType::class, ['label' => 'Порядок']);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id', null, ['label' => 'ID'])
            ->add('product', null, ['label' => 'Товар'])
            ->add('name', null, ['label' => 'Имя'])
            ->addIdentifier('title', null, ['label' => 'Название'])
            ->add('value', null, ['label' => 'Значение']);
    }
}