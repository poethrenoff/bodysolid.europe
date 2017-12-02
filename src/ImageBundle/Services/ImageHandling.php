<?php

namespace ImageBundle\Services;

use ImageBundle\Entity\Image;
use Symfony\Component\Asset\Packages;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Image manipulation service.
 */
class ImageHandling
{
    /**
     * @var string
     */
    protected $webDirectory;

    /**
     * @var string
     */
    protected $cacheDirectory;

    /**
     * @var int
     */
    protected $cacheDirMode;

    /**
     * @var bool
     */
    protected $throwException;

    /**
     * @var Packages
     */
    protected $assetPackages;

    /**
     * @param Packages $assetPackages
     * @param ContainerInterface $container
     */
    public function __construct(Packages $assetPackages,
                                ContainerInterface $container)
    {
        $this->assetPackages = $assetPackages;

        $this->webDirectory = $container->getParameter('image.web_dir');
        $this->cacheDirectory = $container->getParameter('image.cache_dir');
        $this->cacheDirMode = octdec($container->getParameter('image.cache_dir_mode'));
        $this->throwException = $container->getParameter('image.throw_exception');
        $this->fallbackImage = $container->getParameter('image.fallback_image');
    }

    /**
     * Get a manipulable image instance.
     *
     * @param string $file the image path
     *
     * @return Image a manipulable image instance
     */
    public function open($file)
    {
        $image = new Image($this->webDirectory . '/' . $file);

        $image->setCacheDir($this->cacheDirectory);
        $image->setCacheDirMode($this->cacheDirMode);
        $image->setActualCacheDir($this->webDirectory . '/' . $this->cacheDirectory);
        $image->useFallback(!$this->throwException);
        $image->setFallback($this->fallbackImage);

        $image->setFileCallback(function ($file) {
            return $this->assetPackages->getUrl($file);
        });

        return $image;
    }
}
