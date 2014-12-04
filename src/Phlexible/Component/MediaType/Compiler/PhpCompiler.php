<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MediaType\Compiler;

use CG\Core\DefaultGeneratorStrategy;
use CG\Generator\PhpClass;
use CG\Generator\PhpMethod;
use Phlexible\Component\MediaType\Model\MediaTypeCollection;

/**
 * PHP compiler
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
        $className = $this->getClassname();

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

        $constructor = PhpMethod::create('__construct');
        $constructor->setBody($constructorBody);

        $getHashBody = 'return "' . $mediaTypes->getHash() . '";';

        $getHashMethod = PhpMethod::create('getHash');
        $getHashMethod->setBody($getHashBody);

        $class = PhpClass::create($className)
            ->setFinal(true)
            ->setParentClassName('Phlexible\Component\MediaType\Model\MediaTypeCollection')
            ->setMethod($constructor)
            ->setMethod($getHashMethod);

        $generator = new DefaultGeneratorStrategy();

        return "<?php\n\n" . $generator->generate($class);
    }
}
