<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\CmsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Dump router command
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DumpRouterCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('cms:router:dump')
            ->setDescription('Dump router.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $router = $container->get('cmsFrontRouter');

        foreach ($router->getRoutes() as $name => $route) {
            $vars = $route->getVariables();
            $data = [];
            foreach ($vars as $var) {
                $data[$var] = '{' . $var . '}';
            }

            try {
                $assembled = $route->assemble($data);
            } catch (\Exception $e) {
                $assembled = '-';
            }

            $output->writeln(str_pad($name, 40) . ' ' . str_pad(get_class($route), 35) . ' ' . $assembled);
        }

        return 0;
    }
}
