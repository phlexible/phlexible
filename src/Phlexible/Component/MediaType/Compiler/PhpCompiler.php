<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaType\Compiler;

use Phlexible\Component\MediaType\Model\MediaTypeCollection;

/**
 * PHP compiler.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhpCompiler implements CompilerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getClassname()
    {
        return 'Phlexible\Component\MediaType\Model\MediaTypeCollectionCompiled';
    }

    /**
     * {@inheritdoc}
     */
    public function compile(MediaTypeCollection $mediaTypes)
    {
        $parts = explode('\\', $this->getClassname());
        $className = array_pop($parts);
        $namespace = implode('\\', $parts);

        $constructorBody = '';
        foreach ($mediaTypes->all() as $mediaType) {
            $titles = count($mediaType->getTitles()) ? var_export($mediaType->getTitles(), true) : 'array()';
            $mimetypes = count($mediaType->getMimetypes()) ? var_export(
                $mediaType->getMimetypes(),
                true
            ) : 'array()';
            $icons = count($mediaType->getIcons()) ? var_export(
                $mediaType->getIcons(),
                true
            ) : 'array()';

            $constructorBody .= <<<EOF
        \$this->add(
            \$this->create()
                ->setName("{$mediaType->getName()}")
                ->setCategory("{$mediaType->getCategory()}")
                ->setTitles({$titles})
                ->setMimetypes({$mimetypes})
                ->setIcons({$icons})
        );

EOF;
        }

        $constructor = <<<EOF
    /**
     * Constructor.
     */
    public function __construct()
    {
$constructorBody
    }
EOF;

        $getHash = <<<EOF
    /**
     * Return hash
     *
     * @return string
     */
    public function getHash()
    {
        return "{$mediaTypes->getHash()}";
    }
EOF;
        $parentClassName = '\Phlexible\Component\MediaType\Model\MediaTypeCollection';

        $class = <<<EOF
/**
 * Compiled MediaTypes
 */
final class $className extends $parentClassName
{
$constructor

$getHash
}
EOF;

        $file = <<<EOF
<?php

namespace $namespace;

$class
EOF;

        return $file;
    }
}
