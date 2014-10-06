<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Command;

use Phlexible\Bundle\ElementBundle\ElementStructure\Diff\Differ;
use Phlexible\Bundle\ElementBundle\Model\ElementStructure;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Update command
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class UpdateCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('elements:update')
            ->setDescription('Update element.')
            ->addArgument('eid', InputArgument::REQUIRED, 'EID');
    }


    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $elementService = $this->getContainer()->get('phlexible_element.element_service');

        $element = $elementService->findElement($input->getArgument('eid'));
        $elementtype = $elementService->findElementtype($element);

        $latestElementVersion = $elementService->findLatestElementVersion($element);
        $latestElementStructure = $elementService->findElementStructure($latestElementVersion);

        $elementStructure = null;
        if ($latestElementStructure->getId()) {
            $elementStructure = clone $latestElementStructure;
        }

        $elementVersion = $elementService->createElementVersion(
            $element,
            $elementStructure,
            null,
            $elementtype->getModifyUserId()
        );

        // TODO: meta, titles
    }

    /**
     * @param ElementStructure $structure
     *
     * @return ElementStructure
     */
    private function iterateStructure(ElementStructure $structure)
    {
        $elementStructure = new ElementStructure();
        $elementStructure
            ->setId($structure->getId())
            ->setDsId($structure->getDsId())
            ->setName($structure->getName())
            //->setParentId($structure->getParentId())
            //->setParentDsId($structure->getParentDsId())
            ->setParentName($structure->getParentName());
        ;

        foreach ($structure->getValues() as $value) {
            $elementStructure->setValue($value);
        }

        foreach ($structure->getStructures() as $childStructure) {
            $elementStructure->addStructure($this->iterateStructure($childStructure));
        }

        return $elementStructure;
    }
}

