<?php

namespace Nassau\PocztaPolskaPnaBundle\Command;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManager;
use Nassau\PocztaPolskaPnaBundle\Entity\PnaCity;
use Nassau\PocztaPolskaPnaBundle\Entity\PnaStreet;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCommand extends ContainerAwareCommand
{
    const OPTION_EXCEPTIONS = 'exceptions';
    const ARGUMENT_FILE     = 'file';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('pna:import')
            ->addArgument(self::ARGUMENT_FILE, InputArgument::OPTIONAL, '', 'php://stdin')
            ->addOption(self::OPTION_EXCEPTIONS, null, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL,
                'Normalize cities with names starting with those values', ['Warszawa', 'Łódź', 'Wrocław', 'Poznań', 'Kraków']);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = file($input->getArgument(self::ARGUMENT_FILE));
        $exceptions = $input->getOption(self::OPTION_EXCEPTIONS);

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $em->beginTransaction();

        $output->write('Trimming database: ');

        $em->getRepository('PocztaPolskaPnaBundle:PnaStreet')->createQueryBuilder('n')->delete()->getQuery()->execute();
        $em->getRepository('PocztaPolskaPnaBundle:PnaCity')->createQueryBuilder('n')->delete()->getQuery()->execute();

        $output->writeln('<info>Done</info>');

        $output->writeln('Importing: ');

        // headers
        $headers = ['code', 'city', 'street', 'ranges', 'commune', 'county', 'province'] + explode(";", array_shift($file));

        $progress = new ProgressBar($output, sizeof($file));
        $progress->setRedrawFrequency($progress->getMaxSteps() / 100);
        $progress->start();

        for ($last = null, $index = 0; $index < sizeof($file); $index++) {
            $progress->advance();
            $data = str_getcsv(trim($file[$index]), ';', '"');

            if (sizeof($headers) !== sizeof($data)) {
                continue;
            }

            $data = array_combine($headers, $data);

            $name = array_reduce($data['street'] ? $exceptions : [], function ($name, $exception) {
                if (0 === strpos($name, $exception . ' (')) {
                    return $exception;
                }

                return $name;
            }, $data['city']);

            $city = (new PnaCity)
                ->setCode($data['code'])
                ->setName($name)
                ->setCommune($data['commune'])
                ->setCounty($data['county'])
                ->setProvince($data['province']);

            if ($last instanceof PnaCity) {
                if ($last->getUniqueName() !== $city->getUniqueName()) {

                    try {
                        $em->flush();
                    } catch (UniqueConstraintViolationException $u) {
                        $em = $this->fixDuplicate($em, $last);
                    }

                    $em->detach($last);
                    $em->clear();

                    $em->persist($city);

                } else {
                    $city = $last;
                }
            } else {
                $em->persist($city);
            }

            if ($data['street'] || $data['ranges']) {
                $city->addStreet(new PnaStreet($data['street'] ?: "", $data['code'], $data['ranges']));
            }

            $last = $city;
        }

        $progress->finish();

        $em->flush();

        $output->writeln('');
        $output->writeln('<info>Done!</info>');

        $em->commit();
    }

    /**
     * @param EntityManager $em
     * @param PnaCity $last
     *
     * @return EntityManager
     */
    protected function fixDuplicate(EntityManager $em, PnaCity $last)
    {
        /**
         * Fix the connection & entity manager:
         */
        $connection = $em->getConnection();
        $ref = (new \ReflectionObject($connection))->getProperty('_isRollbackOnly');
        $ref->setAccessible(true);
        $ref->setValue($connection, false);
        $em = $em->create($connection, $em->getConfiguration());

        $existing = $em->getRepository('PocztaPolskaPnaBundle:PnaCity')->findOneByCity($last);

        foreach ($last->getStreets() as $street) {
            $existing->addStreet($street);
        }

        $em->persist($existing);

        $em->flush();

        return $em;
    }
}
