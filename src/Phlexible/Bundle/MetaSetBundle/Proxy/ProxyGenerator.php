<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MetaSetBundle\Proxy;

use CG\Generator\PhpClass;
use CG\Generator\PhpMethod;
use CG\Generator\PhpProperty;
use Phlexible\Bundle\MetaSetBundle\MetaSet;

/**
 * Proxy generator
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ProxyGenerator implements ProxyGeneratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function generate(MetaSet $metaSet)
    {
        $id = $metaSet->getId();
        $title = $metaSet->getTitle();

        $class = PhpClass::create('MetaItem')
            ->setFinal(true)
            ->addInterfaceName('Phlexible\MetaSets\MetaItemInterface');

        foreach ($metaSet->getFields() as $field) {
            $key = $field->getKey();

            $property = PhpProperty::create($key)
                ->setDocblock("/**\n * @var string\n */");


            $getterName = 'get' . ucfirst($key);
            $getter = PhpMethod::create($getterName)
                ->setDocblock("/**\n * @return string\n */");


            $setterName = 'set' . ucfirst($key);
            $setter = PhpMethod::create($setterName)
                ->setDocblock("/**\n * @param string \$value\n * @return \$this\n */")
                ->addParameter('value');

            $class->setProperty($property);
            $class->setMethod($getter);
            $class->setMethod($setter);
        }
    }
}
