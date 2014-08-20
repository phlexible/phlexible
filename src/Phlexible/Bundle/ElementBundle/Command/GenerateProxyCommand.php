<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Command;

use Phlexible\Bundle\ElementBundle\Proxy\ProxyGenerator;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Generate proxy command
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class GenerateProxyCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('elements:proxy:generate')
            ->setDescription('Generate proxy.')
            ->addArgument('elementtypeId', InputArgument::REQUIRED, 'Elementtype ID');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $elementtypeService = $this->getContainer()->get('phlexible_elementtype.elementtype_service');
        $fieldRegistry = $this->getContainer()->get('phlexible_elementtype.field.registry');

        $elementtype = $elementtypeService->findElementtype($input->getArgument('elementtypeId'));
        $elementtypeVersion = $elementtypeService->findLatestElementtypeVersion($elementtype);
        $generator = new ProxyGenerator($elementtypeService);
        $data = $generator->generate($elementtypeVersion, $fieldRegistry);

        $filesystem = new Filesystem();

        $namespace = $data['namespace'];
        $path = $this->getContainer()->getParameter('kernel.root_dir') . 'proxies/' . str_replace('\\', '/', $namespace);

        if ($filesystem->exists($path)) {
            $filesystem->remove($path);
        }
        $filesystem->mkdir($path);

        $output->writeln("Namespace: $namespace");
        $output->writeln("Path: $path");
        foreach ($data['content'] as $classname => $content) {
            $filename = $classname . '.php';
            $pathname = $path . '/' . $filename;
            $output->writeln("Writing $filename...");
            file_put_contents($pathname, $content);
        }

        return 0;
    }
}

