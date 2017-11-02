<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use AppBundle\Entity\Category;

class CategoryAdmin extends AbstractAdmin
{
    protected $datagridValues = array(
        '_sort_by' => 'category_order',
        '_sort_order' => 'ASC',
    );

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('category', EntityType::class, array(
                'class' => Category::class,
                'label' => 'Родительская категория',
                'required' => false,
            ))
            ->add('title', 'text', array('label' => 'Название'));

        if (($category = $this->getSubject()) && !empty($category->getName())) {
            $formMapper
                ->add('name', 'text', array('label' => 'Ссылка'));
        }

        $formMapper
            ->add('sort', 'integer', array('label' => 'Порядок'))
            ->add('active', 'checkbox', array('label' => 'Видимость', 'required' => false));
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('category', null, array('label' => 'Родительская категория'))
            ->add('title', null, array('label' => 'Название'))
            ->add('active', null, array('label' => 'Видимость'));
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id', null, array('label' => 'ID'))
            ->add('category', null, array('label' => 'Родительская категория'))
            ->addIdentifier('title', null, array('label' => 'Название'))
            ->add('name', null, array('label' => 'Ссылка'))
            ->add('sort', null, array('label' => 'Порядок', 'editable' => true))
            ->add('active', null, array('label' => 'Видимость', 'editable' => true));
//            ->add('_action', 'actions', array(
//                'label' => 'Операции',
//                'actions' => array(
//                    'product' => array('template' => 'AppBundle::Admin/product.html.twig')
//                )));
    }
}