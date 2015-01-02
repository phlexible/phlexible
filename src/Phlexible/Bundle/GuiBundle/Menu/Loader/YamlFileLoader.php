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
                throw new LoaderException("Data type of parent has to be string, $name given.");
            }
            if (!isset($handlerData['handle']) || !is_string($handlerData['handle'])) {
                throw new LoaderException("Data type of handle has to be string, $name given.");
            }
            if (isset($handlerData['roles']) && !is_array($handlerData['roles'])) {
                throw new LoaderException("Data type of roles has to be array.");
            }

            if (!isset($handlerData['parent'])) {
                $handlerData['parent'] = null;
            }
            if (!isset($handlerData['roles'])) {
                $handlerData['roles'] = [];
            }

            $handlers->set($name, new MenuItem($handlerData['handle'], $handlerData['parent'], $handlerData['roles']));
        }

        return $handlers;
    }
}
