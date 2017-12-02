<?php

namespace ImageBundle\Twig;

use ImageBundle\Services\ImageHandling;

/**
 * ImageExtension
 */
class ImageExtension extends \Twig_Extension
{
    /**
     * @var ImageHandling
     */
    private $imageHandling;

    /**
     * @param ImageHandling $imageHandling
     */
    public function __construct(ImageHandling $imageHandling)
    {
        $this->imageHandling = $imageHandling;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('image', array($this, 'image'), array('is_safe' => array('html'))),
        );
    }

    /**
     * @param string $path
     *
     * @return object
     */
    public function image($path)
    {
        return $this->imageHandling->open($path);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'image';
    }
}
