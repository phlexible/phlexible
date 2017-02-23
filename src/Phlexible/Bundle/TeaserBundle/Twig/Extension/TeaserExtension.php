<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TeaserBundle\Twig\Extension;

use Phlexible\Bundle\TeaserBundle\ContentTeaser\ContentTeaser;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Fragment\FragmentHandler;
use Symfony\Component\Routing\RouterInterface;

/**
 * Teaser extension.
 *
 * @author Stephan Wentz <sw@symfony.com>
 */
class TeaserExtension extends \Twig_Extension
{
    /**
     * @var FragmentHandler
     */
    private $handler;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param FragmentHandler $handler
     * @param RouterInterface $router
     * @param RequestStack    $requestStack
     */
    public function __construct(FragmentHandler $handler, RouterInterface $router, RequestStack $requestStack)
    {
        $this->handler = $handler;
        $this->router = $router;
        $this->requestStack = $requestStack;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('teaser_render', array($this, 'renderTeaser'), array('is_safe' => array('html'))),
        );
    }

    /**
     * Renders a teaser.
     *
     * @param ContentTeaser $teaser
     * @param array         $parameters
     * @param array         $options
     *
     * @return string The fragment content
     *
     * @see FragmentHandler::render()
     */
    public function renderTeaser(ContentTeaser $teaser, array $parameters = array(), array $options = array())
    {
        $parameters['teaserId'] = $teaser->getId();

        if ($this->requestStack->getMasterRequest()->attributes->get('_preview')) {
            $parameters['preview'] = true;
        }

        $uri = $this->router->generate('teaser_render', $parameters);

        $strategy = isset($options['strategy']) ? $options['strategy'] : 'inline';
        unset($options['strategy']);

        return $this->handler->render($uri, $strategy, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'phlexible_teaser';
    }
}
