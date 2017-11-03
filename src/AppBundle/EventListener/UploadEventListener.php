<?php

namespace AppBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Sonata\AdminBundle\Event\PersistenceEvent;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Cocur\Slugify\Slugify;

class UploadEventListener implements EventSubscriberInterface
{
    /**
     * @var PropertyAccessorInterface
     */
    protected $accessor;

    /**
     * @var array
     */
    protected $uploadParams = [];

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
        foreach ($this->getUploadableFields($object) as $fieldDesc) {
            if (!empty($file = $this->getAccessor()->getValue($object, $fieldDesc['fileField']))) {
                $fileName = (new Slugify())->slugify($file->getClientOriginalName(), [
                    'regexp' => '/([^A-Za-z0-9\.]|-)+/'
                ]);

                $file->move($fieldDesc['directory'], $fileName);

                $filePath = '/' . trim($fieldDesc['alias'], '/') . '/' . $fileName;
                $this->getAccessor()->setValue($object, $fieldDesc['targetField'], $filePath);
                $this->getAccessor()->setValue($object, $fieldDesc['fileField'], null);
            }
        }
    }

    /**
     * @param object $object
     * @return array
     */
    protected function getUploadableFields($object): array
    {
        if (in_array(get_class($object), array_keys($this->getUploadParams())) &&
                is_array($this->getUploadParams()[get_class($object)])) {
            return $this->getUploadParams()[get_class($object)];
        }
        return [];
    }

    /**
     * @return array
     */
    protected function getUploadParams(): array
    {
        if (!empty($this->uploadParams)) {
            return $this->uploadParams;
        }
        if ($this->container->hasParameter('upload_params') &&
                is_array($this->container->getParameter('upload_params'))) {
            return $this->uploadParams = $this->container->getParameter('upload_params');
        }
        return [];
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
