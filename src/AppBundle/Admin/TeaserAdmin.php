<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class TeaserAdmin extends AbstractAdmin
{
    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $imageOptions = ['label' => 'Изображение (url)', 'required' => false];
        if (($subject = $this->getSubject()) && ($webPath = $subject->getImage())) {
            $imageOptions['help'] = '<img src="' . $webPath . '" />';
        }

        $formMapper
            ->add('title', TextType::class, ['label' => 'Название'])
            ->add('imageFile', FileType::class, ['label' => 'Изображение', 'required' => false])
            ->add('image', TextType::class, $imageOptions)
            ->add('url', TextType::class, ['label' => 'Ссылка'])
            ->add('sort', IntegerType::class, ['label' => 'Порядок'])
            ->add('active', CheckboxType::class, ['label' => 'Видимость', 'required' => false]);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id', null, ['label' => 'ID'])
            ->addIdentifier('title', null, ['label' => 'Название'])
            ->add('url', null, ['label' => 'Ссылка'])
            ->add('sort', null, ['label' => 'Порядок', 'editable' => true])
            ->add('active', null, ['label' => 'Видимость', 'editable' => true]);
    }
}