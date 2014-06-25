<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Menu\Loader;

use Phlexible\Bundle\GuiBundle\Menu\MenuItem;
use Phlexible\Bundle\GuiBundle\Menu\MenuItemCollection;
use Symfony\Component\Yaml\Parser;

/**
 * YAML file loader
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class YamlFileLoader implements LoaderInterface
{
    /**
     * @var Parser
     */
    private $parser;

    /**
     * {@inheritdoc}
     */
    public function load($file)
    {
        $this->parser = new Parser();

        $config = $this->import($file);

        return $config;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($file)
    {
        return 'yml' === pathinfo($file, PATHINFO_EXTENSION);
    }

    /**
     * @param string $file
     *
     * @throws LoaderException
     * @return array
     */
    private function import($file)
    {
        $fileData = $this->parser->parse(file_get_contents($file));

        $handlers = new MenuItemCollection();

        foreach ($fileData as $name => $handlerData) {
            if (isset($handlerData['parent']) && !is_string($handlerData['parent'])) {
                throw new LoaderException('Data at index parent has to be a string.' . $name);
            }
            if (!isset($handlerData['xtype']) || !is_string($handlerData['xtype'])) {
                throw new LoaderException('Data at index xtype has to be a string.' . $name);
            }
            if (isset($handlerData['resources']) && !is_array($handlerData['resources'])) {
                throw new LoaderException('Data at index resources has to be an array.');
            }

            if (!isset($handlerData['parent'])) {
                $handlerData['parent'] = null;
            }
            if (!isset($handlerData['resources'])) {
                $handlerData['resources'] = array();
            }

            $handlers->set($name, new MenuItem($handlerData['xtype'], $handlerData['parent'], $handlerData['resources']));
        }

        return $handlers;
    }
}
