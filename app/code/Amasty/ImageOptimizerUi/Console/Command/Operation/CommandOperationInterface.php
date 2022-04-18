<?php
declare(strict_types=1);

namespace Amasty\ImageOptimizerUi\Console\Command\Operation;

interface CommandOperationInterface
{
    /**
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return null|int null or 0 if everything went fine, or an error code
     */
    public function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ): ?int;
}
