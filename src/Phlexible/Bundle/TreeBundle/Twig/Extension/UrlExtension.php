<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\Twig\Extension;

use Phlexible\Bundle\ElementBundle\Model\ElementStructureValue;
use Phlexible\Bundle\TreeBundle\ContentTree\ContentTreeContext;
use Phlexible\Bundle\TreeBundle\ContentTree\ContentTreeManagerInterface;
use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Twig url extension.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class UrlExtension extends \Twig_Extension
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var ContentTreeManagerInterface
     */
    private $contentTreeManager;

    /**
     * @param RouterInterface             $router
     * @param ContentTreeManagerInterface $contentTreeManager
     */
    public function __construct(RouterInterface $router, ContentTreeManagerInterface $contentTreeManager)
    {
        $this->router = $router;
        $this->contentTreeManager = $contentTreeManager;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('path', [$this, 'path']),
            new \Twig_SimpleFunction('url', [$this, 'url']),
        ];
    }

    /**
     * @param string   $name
     * @param array    $parameters
     * @param bool|int $relative
     *
     * @return string
     */
    public function path($name, array $parameters = [], $relative = false)
    {
        if ($name instanceof TreeNodeInterface) {
            return $this->router->generate($name, $parameters, $relative ? UrlGeneratorInterface::RELATIVE_PATH : UrlGeneratorInterface::ABSOLUTE_PATH);
        } elseif ($name instanceof ContentTreeContext) {
            return $this->router->generate($name->getNode(), $parameters, $relative ? UrlGeneratorInterface::RELATIVE_PATH : UrlGeneratorInterface::ABSOLUTE_PATH);
        } elseif ($name instanceof ElementStructureValue) {
            if ($name->getType() === 'link') {
                $link = $name->getValue();
                if ($link['type'] === 'internal' || $link['type'] === 'intrasiteroot') {
                    $tree = $this->contentTreeManager->findByTreeId($link['tid']);
                    if ($tree) {
                        $node = $tree->get($link['tid']);

                        return $this->router->generate(
                            $node,
                            $parameters,
                            $relative ? UrlGeneratorInterface::RELATIVE_PATH : UrlGeneratorInterface::ABSOLUTE_PATH
                        );
                    }
                } elseif ($link['type'] === 'external') {
                    return $link['url'];
                } elseif ($link['type'] === 'mailto') {
                    return 'mailto:'.$link['recipient'];
                }
            }
        } elseif (is_array($name) && isset($name['type']) && in_array($name['type'], array('internal', 'intrasiteroot', 'external', 'mailto'))) {
            $link = $name;
            if ($link['type'] === 'internal' || $link['type'] === 'intrasiteroot') {
                $tree = $this->contentTreeManager->findByTreeId($link['tid']);
                if ($tree) {
                    $node = $tree->get($link['tid']);

                    return $this->router->generate(
                        $node,
                        $parameters,
                        $relative ? UrlGeneratorInterface::RELATIVE_PATH : UrlGeneratorInterface::ABSOLUTE_PATH
                    );
                }
            } elseif ($link['type'] === 'external') {
                return $link['url'];
            } elseif ($link['type'] === 'mailto') {
                return 'mailto:'.$link['recipient'];
            }
        } elseif (strlen($name) && (is_int($name) || (int) $name)) {
            $tree = $this->contentTreeManager->findByTreeId((int) $name);
            if ($tree) {
                $node = $tree->get((int) $name);

                return $this->router->generate($node, $parameters, $relative ? UrlGeneratorInterface::RELATIVE_PATH : UrlGeneratorInterface::ABSOLUTE_PATH);
            }
        } elseif (is_string($name)) {
            return $this->router->generate($name, $parameters, $relative ? UrlGeneratorInterface::RELATIVE_PATH : UrlGeneratorInterface::ABSOLUTE_PATH);
        }

        return '';
    }

    /**
     * @param string   $name
     * @param array    $parameters
     * @param bool|int $schemeRelative
     *
     * @return string
     */
    public function url($name, array $parameters = [], $schemeRelative = false)
    {
        if ($name instanceof TreeNodeInterface) {
            return $this->router->generate($name, $parameters, $schemeRelative ? UrlGeneratorInterface::NETWORK_PATH : UrlGeneratorInterface::ABSOLUTE_URL);
        } elseif ($name instanceof ContentTreeContext) {
            return $this->router->generate($name->getNode(), $parameters, $schemeRelative ? UrlGeneratorInterface::NETWORK_PATH : UrlGeneratorInterface::ABSOLUTE_URL);
        } elseif ($name instanceof ElementStructureValue) {
            if ($name->getType() === 'link') {
                $link = $name->getValue();
                if ($link['type'] === 'internal' || $link['type'] === 'intrasiteroot') {
                    $tree = $this->contentTreeManager->findByTreeId($link['tid']);
                    if ($tree) {
                        $node = $tree->get($link['tid']);

                        return $this->router->generate($node, $parameters, $schemeRelative ? UrlGeneratorInterface::NETWORK_PATH : UrlGeneratorInterface::ABSOLUTE_URL);
                    }
                } elseif ($link['type'] === 'external') {
                    return $link['url'];
                } elseif ($link['type'] === 'mailto') {
                    return 'mailto:'.$link['recipient'];
                }
            }
        } elseif (is_array($name) && isset($name['type']) && in_array($name['type'], array('internal', 'intrasiteroot', 'external', 'mailto'))) {
            $link = $name;
            if ($link['type'] === 'internal' || $link['type'] === 'intrasiteroot') {
                $tree = $this->contentTreeManager->findByTreeId($link['tid']);
                if ($tree) {
                    $node = $tree->get($link['tid']);

                    return $this->router->generate($node, $parameters, $schemeRelative ? UrlGeneratorInterface::NETWORK_PATH : UrlGeneratorInterface::ABSOLUTE_URL);
                }
            } elseif ($link['type'] === 'external') {
                return $link['url'];
            } elseif ($link['type'] === 'mailto') {
                return 'mailto:'.$link['recipient'];
            }
        } elseif (strlen($name) && (is_int($name) || (int) $name)) {
            $tree = $this->contentTreeManager->findByTreeId((int) $name);
            if ($tree) {
                $node = $tree->get((int) $name);

                return $this->router->generate($node, $parameters, $schemeRelative ? UrlGeneratorInterface::NETWORK_PATH : UrlGeneratorInterface::ABSOLUTE_URL);
            }
        } elseif (is_string($name)) {
            return $this->router->generate($name, $parameters, $schemeRelative ? UrlGeneratorInterface::NETWORK_PATH : UrlGeneratorInterface::ABSOLUTE_URL);
        }

        return '';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'phlexible_url';
    }
}
