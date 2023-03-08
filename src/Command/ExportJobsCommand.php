<?php

namespace App\Command;

use DateTime;
use App\Entity\Job;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ExportJobsCommand extends Command
{
    protected static $defaultName = 'app:export-jobs';

    private $entityManager;
    private $serializer;

    public function __construct(EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Export jobs to CSV')
            ->addOption('since', 's', InputOption::VALUE_REQUIRED, 'Filter jobs created since this date (inclusive)');
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $since = $input->getOption('since');
        if (!$since) {
            $jobs = $this->entityManager->getRepository(Job::class)->findAll();
        } else {
            $since = new \DateTime($input->getOption('since'));
            $jobs = $this->entityManager->getRepository(Job::class)->findBySince($since);
        }

        if (0 === count($jobs)) {
            $output->writeln('No jobs to export.');

            return Command::SUCCESS;
        }

        $fileName = sprintf('export_%s.csv', date('Y-m-d'));
        $filePath = sprintf('%s/%s', './var', $fileName);

        $csv = $this->serializer->serialize($jobs, 'csv', [
            'groups' => 'csv',
            'csv_headers' => ['id', 'title', 'description', 'createdAt', 'updatedAt'],
            'csv_delimiter' => ';',
            'csv_enclosure' => '"',
            'csv_escape_char' => '\\',
        ]);

        file_put_contents($filePath, $csv);

        $output->writeln(sprintf('Exported %d jobs to %s', count($jobs), $filePath));

        return Command::SUCCESS;
    }
}
