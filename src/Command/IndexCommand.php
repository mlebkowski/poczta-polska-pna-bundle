<?php

namespace Nassau\PocztaPolskaPnaBundle\Command;

use AlgoliaSearch\Index;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Nassau\PocztaPolskaPnaBundle\Services\Indexer\Indexer;
use Nassau\PocztaPolskaPnaBundle\Services\Indexer\IndexerException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class IndexCommand extends ContainerAwareCommand
{
    const OPTION_CLEANUP = 'cleanup';

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('pna:index')->addOption(self::OPTION_CLEANUP);
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        /** @var Indexer $indexer */
        $indexer = $container->get('nassau_pna.indexer', ContainerInterface::NULL_ON_INVALID_REFERENCE);
        $entityManager = $container->get('doctrine.orm.entity_manager');
        $query = $entityManager->getRepository('PocztaPolskaPnaBundle:PnaCity')->createQueryBuilder('pna')->getQuery();

        if (null === $indexer) {
            throw new \RuntimeException('You need to configure the `index_name` setting for indexer to work');
        }

        $paginator = new Paginator($query);
        $batchSize = 10 * 1000;
        $max = sizeof($paginator);

        $progress = new ProgressBar($output);
        $progress->start($max);

        $transaction = $this->beginTransaction($indexer, (bool)$input->getOption(self::OPTION_CLEANUP));

        $currentPage = 0;
        do {
            $results = $paginator
                ->getQuery()
                ->setFirstResult($batchSize * $currentPage)
                ->setMaxResults($batchSize)
                ->getResult();

            $indexer->batchIndex($results, $transaction);

            $progress->advance($batchSize);
            $entityManager->clear();

        } while ($max > ++$currentPage * $batchSize);

        $indexer->commit($transaction);
    }

    /**
     * @param $indexer
     * @param $cleanup
     *
     * @return Index
     * @throws IndexerException
     */
    protected function beginTransaction(Indexer $indexer, $cleanup)
    {
        try {
            return $indexer->beginTransaction($cleanup);
        } catch (IndexerException $e) {
            if ($cleanup) {
                throw $e;
            }

            $message = sprintf('Cannot start indexing transaction. Try using the --%s option', self::OPTION_CLEANUP);
            throw new \RuntimeException($message, 0, $e);
        }
    }


}
