<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\Command;

use Phlexible\Bundle\ElementBundle\Entity\ElementVersion;
use Phlexible\Bundle\TreeBundle\Entity\TreeNodeOnline;
use Phlexible\Bundle\TreeBundle\Tree\TreeIterator;
use RecursiveIteratorIterator;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GenerateHashesCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('element:generate:hashes')
            ->setDescription('Generate hashes elements.')
            ->addArgument('eid', InputArgument::OPTIONAL, 'EID');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $style = new SymfonyStyle($input, $output);

        $nodeHasher = $this->getContainer()->get('phlexible_tree.node_hasher');

        $entityManager = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        $entityManager->getConfiguration()->setSQLLogger(null);
        $entityManager->getConnection()->getConfiguration()->setSQLLogger(null);
        $onlineRepo = $entityManager->getRepository(TreeNodeOnline::class);

        $criteria = array();
        if ($eid = $input->getArgument('eid')) {
            $criteria['eid'] = $eid;
        }

        $cnt = $onlineRepo->createQueryBuilder('c')->select('COUNT(c.id)')->getQuery()->getSingleScalarResult();

        $limit = 100;
        $offset = 0;

        while ($onlineNodes = $onlineRepo->findBy([], ['id' => 'ASC'], $limit, $offset)) {
            foreach ($onlineNodes as $index => $onlineNode) {
                $output->write(($offset+$index+1).' / '.$cnt.' | '.number_format(memory_get_usage(true) / 1024 / 1024, 2).' mb | ');
                $output->write($onlineNode->getTreeNode()->getId().' '.$onlineNode->getVersion().' '.$onlineNode->getLanguage().' ... ');
                $hash = $nodeHasher->hashNode($onlineNode->getTreeNode(), $onlineNode->getVersion(), $onlineNode->getLanguage());
                if ($hash !== $onlineNode->getHash()) {
                    $output->writeln('generated');
                    $onlineNode->setHash($hash);
                } else {
                    $output->writeln('up-to-date');
                }
            }

            $entityManager->flush();
            $offset += $limit;
        }

        $style->writeln('');
        $style->writeln('Done');

        return 0;
    }
}
