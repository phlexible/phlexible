<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Menu\Loader;

/**
 * Delgating loader
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DelegatingLoader implements LoaderInterface
{
    /**
     * @var LoaderResolverInterface
     */
    private $resolver;

    /**
     * @param LoaderResolverInterface $resolver
     */
    public function __construct(LoaderResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * {@inheritdoc}
     */
    public function load($file)
    {
        if (null === $loader = $this->resolver->resolve($file)) {
            throw new LoaderResolverException("No resolver found for file $file.");
        }

        return $loader->load($file);
    }

    /**
     * {@inheritdoc}
     */
    public function supports($file)
    {
        return null === $this->resolver->resolve($file) ? false : true;
    }
}