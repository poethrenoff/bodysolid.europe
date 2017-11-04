<?php

namespace AdminUploadBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Sonata\AdminBundle\Event\PersistenceEvent;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use AdminUploadBundle\Util\Util;

class AdminUploadEventListener implements EventSubscriberInterface
{
    /**
     * @var PropertyAccessorInterface
     */
    protected $accessor;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * UploadEventListener constructor
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'sonata.admin.event.persistence.pre_update' => 'upload',
            'sonata.admin.event.persistence.pre_persist' => 'upload',
        ];
    }

    /**
     * @param PersistenceEvent $event
     */
    public function upload(PersistenceEvent $event)
    {
        $object = $event->getObject();
        $uploadParams = $this->container->getParameter('admin_upload')['entities'];
        if (in_array(get_class($object), array_keys($uploadParams)) ) {
            $fieldDescs = $uploadParams[get_class($object)];
            foreach ($fieldDescs as $fieldName => $fieldDesc) {
                $file = $this->getAccessor()->getValue($object, $fieldDesc['fileField']);
                if ($file) {
                    $fileName = Util::getFileName($file->getClientOriginalName());
                    $file->move($fieldDesc['directory'], $fileName);
                    $filePath = Util::getFilePath($fileName, $fieldDesc['alias']);

                    $this->getAccessor()->setValue($object, $fieldName, $filePath);
                    $this->getAccessor()->setValue($object, $fieldDesc['fileField'], null);
                }
            }
        }
    }

    /**
     * @return PropertyAccessorInterface
     */
    protected function getAccessor(): PropertyAccessorInterface
    {
        if ($this->accessor !== null) {
            return $this->accessor;
        }
        return $this->accessor = PropertyAccess::createPropertyAccessor();
    }
}
