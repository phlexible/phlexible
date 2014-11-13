<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * List command
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ListCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('elementtypes:list')
            ->setDescription('List element type.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        $table = new Table($output);
        $table->setHeaders(['ID', 'Unique ID', 'Title']);

        $elementtypeService = $container->get('phlexible_elementtype.elementtype_service');
        foreach ($elementtypeService->findAllElementtypes() as $elementtype) {
            $table->addRow([$elementtype->getId(), $elementtype->getUniqueId(), $elementtype->getTitle()]);
        }

        $table->render();

        return 0;
    }
}
