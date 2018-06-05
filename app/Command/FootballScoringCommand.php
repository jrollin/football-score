<?php
// src/Command/CreateUserCommand.php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
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
            ->setHelp('Gell all possible combinations of scoring to get the given results');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $choices = [3, 6, 7];

        $teamA = (int)$input->getArgument("home");
        $teamB = (int)$input->getArgument("visitor");

        // team A
        $this->combination(0, [], $choices, 0, $teamA, 50);

        // display team A
        $resultTeamA = $this->results;
        $teamA = [];
        foreach ($resultTeamA as $r) {
            $teamA[] = sprintf('[%s]', implode(',', $r));
        }
        $output->writeln(sprintf('Results Team A (%d) :', count($resultTeamA)));
        $output->writeln(implode(PHP_EOL, $teamA));

        //reset
        $this->results = [];

        //team B
        $this->combination(0, [], $choices, 0, $teamB, 50);
        // display team  B
        $resultTeamB = $this->results;
        $teamB = [];
        foreach ($resultTeamB as $r) {
            $teamB[] = sprintf('[%s]', implode(',', $r));
        }
        $output->writeln(sprintf('Results Team B (%d) :', count($resultTeamB)));
        $output->writeln(implode(PHP_EOL, $teamB));
    }


    private $results = [];

    public function combination($parent, $path, $choices, $currentScore, $expectedScore, $maxDepth = 100)
    {
        // sanity check
        if (count($parent) >= $maxDepth) {
            return;
        }

        // break on first success or wrong score because  3 < 6 < 7
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

}



