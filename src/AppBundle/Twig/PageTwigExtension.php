<?php

namespace AppBundle\Twig;

use Symfony\Component\HttpFoundation\RequestStack;

/**
 * PageTwigExtension
 */
class PageTwigExtension extends \Twig_Extension
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_Function('page', array($this, 'page')),
        ];
    }

    /**
     * @param int $page
     * @param string $name
     *
     * @return string
     */
    public function page($page, $name = 'page')
    {
        $request = $this->requestStack->getCurrentRequest();

        $baseUrl = $request->getBaseUrl() . $request->getPathInfo();
        $pageQuery = http_build_query(array_merge($request->query->all(), [$name => $page]));

        return $baseUrl . '?' . $pageQuery;
    }
}
