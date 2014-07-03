<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\FrontendBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Test command
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TestCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('frontend:test')
            ->setDescription('Test request');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $_SERVER['SCRIPT_FILENAME'] = '/app_dev.php';
        $_SERVER['SCRIPT_NAME'] = 'app_dev.php';
        $url = 'http://brainbits.stephan.brainbits-gmbh.local/app_dev.php/de/text1.1192.html';
        $request = Request::create($url, 'GET', array(), array(), array(), $_SERVER);

        $router = $this->getContainer()->get('frontend.router');
        //$router->getRequestContext()->setBaseUrl('/app_dev.php');

        $treeNode = $router->matchRequest($request);

        $generatedUrl = $router->generate($treeNode, array());

        echo $url . PHP_EOL . $generatedUrl . PHP_EOL;

        $configurator = $this->getContainer()->get('elementrenderer.configurator');
        $config = $configurator->configure($request, array());

        $renderer = $this->getContainer()->get('dwoorenderer.renderer');
        echo $renderer->render($config);

        return 0;
    }

}

