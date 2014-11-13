<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\CmsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Match router command
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MatchRouterCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('cms:router:match')
            ->setDefinition([
                new InputArgument('uri', InputArgument::REQUIRED, 'URI to match'),
            ])
            ->setDescription('Match frontend route.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $router = $container->get('cmsFrontRouter');

        $uri = $input->getArgument('uri');

        if (substr($uri, 0, 4) !== 'http') {
            if (substr($uri, 0, 1) !== '/') {
                $uri = '/' . $uri;
            }
            $uri = 'http://example.com' . $uri;
        }

        $route = $router->route($request);
        $output->writeln(print_r($route, 1));

        return 0;
    }

}
