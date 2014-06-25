<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\DocumenttypeBundle\Compiler;

use CG\Core\DefaultGeneratorStrategy;
use CG\Generator\PhpClass;
use CG\Generator\PhpMethod;
use Phlexible\Bundle\DocumenttypeBundle\Model\DocumenttypeCollection;

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
        return 'Phlexible\Bundle\DocumenttypeBundle\Model\DocumenttypeCollection_compiled';
    }

    /**
     * {@inheritdoc}
     */
    public function compile(DocumenttypeCollection $documenttypes)
    {
        $className = $this->getClassname();

        $constructorBody = '';
        foreach ($documenttypes->getAll() as $documenttype) {
            $titles = count($documenttype->getTitles()) ? var_export($documenttype->getTitles(), true) : 'array()';
            $mimetypes = count($documenttype->getMimetypes()) ? var_export($documenttype->getMimetypes(), true) : 'array()';

            $constructorBody .= <<<EOF
\$this->add(
    \$this->create()
        ->setKey("{$documenttype->getKey()}")
        ->setType("{$documenttype->getType()}")
        ->setTitles({$titles})
        ->setMimetypes({$mimetypes})
);
EOF;
        }

            $constructor = PhpMethod::create('__construct');
            $constructor->setBody($constructorBody);

            $getHashBody = 'return "' . $documenttypes->getHash() . '";';

            $getHashMethod = PhpMethod::create('getHash');
            $getHashMethod->setBody($getHashBody);

            $class = PhpClass::create($className)
                ->setFinal(true)
                ->setParentClassName('Phlexible\Bundle\DocumenttypeBundle\Model\DocumenttypeCollection')
                ->setMethod($constructor)
                ->setMethod($getHashMethod);

        $generator = new DefaultGeneratorStrategy();

        return "<?php\n\n".$generator->generate($class);
    }
}