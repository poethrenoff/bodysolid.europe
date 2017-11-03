<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use AppBundle\Entity\Category;

class CategoryAdmin extends AbstractAdmin
{
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
            ->add('externalId', IntegerType::class, ['label' => 'Neotren ID', 'required' => false])
            ->add('category', EntityType::class, ['class' => Category::class, 'label' => 'Родительская категория', 'required' => false,])
            ->add('title', TextType::class, array('label' => 'Название'));

        if (($category = $this->getSubject()) && !empty($category->getName())) {
            $formMapper
                ->add('name', TextType::class, ['label' => 'Ссылка']);
        }

        $formMapper
            ->add('sort', IntegerType::class, ['label' => 'Порядок'])
            ->add('active', CheckboxType::class, ['label' => 'Видимость', 'required' => false]);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('category', null, array('label' => 'Родительская категория'))
            ->add('title', null, array('label' => 'Название'))
            ->add('active', null, array('label' => 'Видимость'));
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id', null, ['label' => 'ID'])
            ->add('category', null, ['label' => 'Родительская категория'])
            ->addIdentifier('title', null, ['label' => 'Название'])
            ->add('name', null, ['label' => 'Ссылка'])
            ->add('sort', null, ['label' => 'Порядок', 'editable' => true])
            ->add('active', null, ['label' => 'Видимость', 'editable' => true])
            ->add('_action', 'actions', [
                'label' => 'Операции',
                'actions' => [
                    'product' => array('template' => 'AppBundle::Admin/product.html.twig')
                ]]);
    }
}