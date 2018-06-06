<?php
// src/Command/CreateUserCommand.php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FootballScoringCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('football:score')
            ->setDescription('find combinations for scores')
            ->addArgument('home', InputArgument::REQUIRED, 'The first team')
            ->addArgument('visitor', InputArgument::REQUIRED, 'The second team')
            ->addOption('depth', 'd', InputOption::VALUE_OPTIONAL, 'limit depth recursion', 50)
            ->setHelp('Gell all possible combinations of scoring to get the given results');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // tree values :
        $choices = [3, 6, 7];

        $teamA = (int)$input->getArgument("home");
        $teamB = (int)$input->getArgument("visitor");
        $maxDepth = (int)$input->getOption('depth');

        // higher points scored during one match is 113 (72-41)...
        if ($maxDepth > 100) {
            $output->writeln(sprintf('<comment>At least %d points for one team, this is a rocket team ? </comment>', 3 * $maxDepth));
        }

        // team A
        $this->combination(0, [], $choices, 0, $teamA, $maxDepth);
        $resultTeamA = $this->results;
        $teamA = [];
        foreach ($resultTeamA as $r) {
            $teamA[] = sprintf('[%s]', implode(',', $r));
        }

        if ($output->isVerbose()) {
            $output->writeln(sprintf('Results Team A (%d) :', count($resultTeamA)));
            $output->writeln(implode(PHP_EOL, $teamA));
        }

        //reset temp store
        $this->results = [];

        //team B
        $this->combination(0, [], $choices, 0, $teamB, $maxDepth);
        $resultTeamB = $this->results;
        $teamB = [];
        foreach ($resultTeamB as $r) {
            $teamB[] = sprintf('[%s]', implode(',', $r));
        }

        if ($output->isVerbose()) {
            $output->writeln(sprintf('Results Team B (%d) :', count($resultTeamB)));
            $output->writeln(implode(PHP_EOL, $teamB));
        }

        // display all results
        $results = $this->getAllTeamScores($teamA, $teamB);
        $output->writeln(sprintf('All scores (%d) :', count($results)));
        foreach ($results as $r) {
            $output->writeln(implode(',', $r));
        }
    }

    /**
     * store valid paths
     *
     * @var array
     */
    private $results = [];


    /**
     * recursive tree traversal
     *
     * nb : 1 point is only possible after 6 points, so we use 7
     * when 7 is in path, we convert it in 6 + 1
     *
     * @param $parent
     * @param $path
     * @param $choices
     * @param $currentScore
     * @param $expectedScore
     * @param $maxDepth
     */
    public function combination($parent, $path, $choices, $currentScore, $expectedScore, $maxDepth)
    {
        // sanity check
        if (count($parent) > $maxDepth) {
            return;
        }

        // break on first success match or wrong score because  3 < 6 < 7
        foreach ($choices as $choice) {
            // matched score
            if ($currentScore + $choice === $expectedScore) {
                $this->results[] = array_merge($path, ($choice === 7) ? [6, 1] : [$choice]);
                break;
            }
            // skip if higher
            if ($currentScore + $choice > $expectedScore) {
                break;
            }
            $this->combination(
                $parent++,
                array_merge($path, ($choice === 7) ? [6, 1] : [$choice]),
                $choices,
                $currentScore + $choice,
                $expectedScore,
                $maxDepth
            );
        }
    }

    /**
     * @param array $teamA
     * @param array $teamB
     * @return array
     */
    public function getAllTeamScores(array $teamA, array $teamB)
    {
        $results = [];
        // break on first success match or wrong score because  3 < 6 < 7
        foreach ($teamA as $pathA) {
            foreach ($teamB as $pathB) {
                $results[] = [$pathA, $pathB];

            }
        }
        return $results;
    }

}



