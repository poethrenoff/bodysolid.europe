<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use AppBundle\Entity\Category;
use AppBundle\Entity\Brand;

class ProductAdmin extends AbstractAdmin
{
    /**
     * {@inheritdoc}
     */
    protected $parentAssociationMapping = 'category';

    /**
     * {@inheritdoc}
     */
    protected $datagridValues = [
        '_sort_by' => 'title',
        '_sort_order' => 'asc',
    ];

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('externalId', IntegerType::class, ['label' => 'Neotren ID', 'required' => false])
            ->add('category', EntityType::class, ['class' => Category::class, 'label' => 'Категория'])
            ->add('brand', EntityType::class, ['class' => Brand::class, 'label' => 'Производитель'])
            ->add('title', TextType::class, ['label' => 'Название'])
            ->add('price', NumberType::class, ['label' => 'Цена'])
            ->add('description', TextareaType::class, ['label' => 'Подробное описание', 'required' => false, 'attr' => ['class' => 'editor']])
            ->add('status', ChoiceType::class, ['label' => 'Статус', 'choices' => [
                'В наличии' => 'available',
                'Ожидается' => 'delivery',
                'Под заказ' => 'order',
            ]])
            ->add('best', CheckboxType::class, ['label' => 'Лучший товар', 'required' => false])
            ->add('active', CheckboxType::class, ['label' => 'Видимость', 'required' => false]);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('brand', null, ['label' => 'Производитель'])
            ->add('category', null, ['label' => 'Категория'])
            ->add('title', null, ['label' => 'Название'])
            ->add('status', null, ['label' => 'Статус'])
            ->add('best', null, ['label' => 'Лучший товар'])
            ->add('active', null, ['label' => 'Видимость']);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id', null, ['label' => 'ID'])
            ->add('category', null, ['label' => 'Категория'])
            ->add('brand', null, ['label' => 'Производитель'])
            ->addIdentifier('title', null, ['label' => 'Название'])
            ->add('price', null, ['label' => 'Цена', 'editable' => true])
            ->add('status', null, ['label' => 'Статус'])
            ->add('active', null, ['label' => 'Видимость', 'editable' => true])
            ->add('_action', 'actions', [
                'label' => 'Операции',
                'actions' => [
                    'product_picture' => ['template' => 'AppBundle::Admin/product_picture.html.twig'],
                    'product_file' => ['template' => 'AppBundle::Admin/product_file.html.twig'],
                    'product_video' => ['template' => 'AppBundle::Admin/product_video.html.twig'],
                    'product_property' => ['template' => 'AppBundle::Admin/product_property.html.twig'],
                ]]);
    }
}