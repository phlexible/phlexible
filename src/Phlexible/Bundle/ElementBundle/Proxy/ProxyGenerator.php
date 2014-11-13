<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Proxy;

use CG\Core\DefaultGeneratorStrategy;
use CG\Generator\PhpClass;
use CG\Generator\PhpMethod;
use CG\Generator\PhpParameter;
use CG\Generator\PhpProperty;
use Phlexible\Bundle\ElementtypeBundle\ElementtypeService;
use Phlexible\Bundle\ElementtypeBundle\ElementtypeVersion\ElementtypeVersion;
use Phlexible\Bundle\ElementtypeBundle\Field\FieldRegistry;

/**
 * Proxy generator
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ProxyGenerator
{
    /**
     * @var ElementtypeService
     */
    private $elementtypeService;

    /**
     * @param ElementtypeService $elementtypeService
     */
    public function __construct(ElementtypeService $elementtypeService)
    {
        $this->elementtypeService = $elementtypeService;
    }

    /**
     * @param ElementtypeVersion $elementtypeVersion
     * @param FieldRegistry      $fieldRegistry
     *
     * @return array
     */
    public function generate(ElementtypeVersion $elementtypeVersion, FieldRegistry $fieldRegistry)
    {
        $generator = new DefaultGeneratorStrategy();

        $distiller = new \Phlexible\Bundle\ElementtypeBundle\Distiller\Distiller($this->elementtypeService, $fieldRegistry);
        $data = $distiller->distill($elementtypeVersion, $fieldRegistry);

        $classname = ucfirst(strtolower($elementtypeVersion->getTitle()));
        $namespace = 'Phlexible\\ElementProxy\\' . $classname;
        $fqName = $namespace . '\\' . $classname;

        $items = [];
        foreach ($data as $item) {
            if (!isset($item['children'])) {
                $items[] = $item;
            }
        }

        $class = $this->generateClass($fqName, $data);
        $classes = $this->generateSubClasses($namespace, $classname, $class, $data);

        $data = [
            'namespace' => $namespace,
            'content'   => [
                $classname => '<?php' . PHP_EOL . PHP_EOL . $generator->generate($class),
            ]
        ];
        foreach ($classes as $classname => $class) {
            $data['content'][$classname] = '<?php' . PHP_EOL . PHP_EOL . $generator->generate($class);
        }

        return $data;
    }

    /**
     * @param string   $namespace
     * @param string   $prefix
     * @param PhpClass $parentClass
     * @param array    $items
     *
     * @return array
     */
    private function generateSubClasses($namespace, $prefix, PhpClass $parentClass, array $items)
    {
        $classes = [];
        foreach ($items as $item) {
            if (isset($item['children'])) {
                $name = ucfirst($this->toCamelCase($item['node']->getWorkingTitle()));
                $classname = $prefix . $name;
                $fqName = $namespace . '\\' . $classname;
                $class = $this->generateClass($fqName, $item['children']);
                $classes[$classname] = $class;
                $lname = lcfirst($name);

                $parentClass
                    ->addUseStatement('Doctrine\Common\Collections\ArrayCollection');
                $constr = $parentClass->getMethod('__construct');
                $constr->setBody($constr->getBody() . "\$this->{$lname}s = new ArrayCollection();\n");

                $adder = PhpMethod::create('add' . $name)
                    ->addParameter(PhpParameter::create($lname)->setType($fqName))
                    ->setDocblock("/**\n * @param $classname \$$lname\n * @return \$this\n */")
                    ->setBody("\$this->{$lname}s->add($lname);\nreturn \$this;");

                $getter = PhpMethod::create('get' . $name . 's')
                    ->setDocblock("/**\n * @return {$classname}[]\n */")
                    ->setBody("return \$this->{$lname}s->getValues();");

                $value = PhpProperty::create($name . 's')
                    ->setDocblock("\n/** @var {$classname}[] */\n");

                $parentClass
                    ->setProperty($value)
                    ->setMethod($adder)
                    ->setMethod($getter);

                $classes = array_merge(
                    $classes,
                    $this->generateSubClasses($namespace, $name, $class, $item['children'])
                );
            }
        }

        return $classes;
    }

    /**
     * @param string $className
     * @param array  $items
     *
     * @return $this
     */
    private function generateClass($className, array $items)
    {
        $class = PhpClass::create($className)
            ->setMethod(PhpMethod::create('__construct'))
            ->setFinal(true);

        foreach ($items as $item) {
            if (isset($item['children'])) {
                continue;
            }

            $node = $item['node'];
            $field = $item['field'];
            $dataType = $field->getDataType();

            $valueName = lcfirst($this->toCamelCase($node->getWorkingTitle()));
            $nodeName = ucfirst($valueName);
            $getterName = 'get' . $nodeName;
            $setterName = 'set' . $nodeName;

            $value = PhpProperty::create($valueName)->setDocblock("\n/** @var $dataType */\n");
            $class->setProperty($value);
            $getter = PhpMethod::create($getterName)
                ->setDocblock("/**\n * @return $dataType\n */")
                ->setBody("return \$this->$valueName;");
            $class->setMethod($getter);

            $setterValue = PhpParameter::create($valueName);
            $setter = PhpMethod::create($setterName)
                ->setDocblock("/**\n * @param $dataType \$$valueName\n * @return \$this\n */")
                ->setBody("\$this->$valueName = \$$valueName;\nreturn \$this;")
                ->addParameter($setterValue);
            $class->setMethod($setter);

        }

        return $class;
    }

    /**
     * @param string $str
     * @param bool   $capitaliseFirstChar
     *
     * @return string
     */
    private function toCamelCase($str, $capitaliseFirstChar = true)
    {
        if ($capitaliseFirstChar) {
            $str[0] = strtoupper($str[0]);
        }
        $func = create_function('$c', 'return strtoupper($c[1]);');

        return preg_replace_callback('/_([a-z])/', $func, $str);
    }
}
