<?php

  namespace Funivan\Cs\Console;

  use Funivan\Cs\Configuration\ConfigurationInterface;
  use Funivan\Cs\Configuration\CsConfiguration;
  use Funivan\Cs\FileProcessor\FixerProcessor;
  use Funivan\Cs\Report\Report;
  use Symfony\Component\Console\Input\InputInterface;
  use Symfony\Component\Console\Input\InputOption;
  use Symfony\Component\Console\Output\OutputInterface;

  /**
   * @author Ivan Shcherbak <dev@funivan.com> 2016
   */
  class FixCommand extends BaseCommand {

    /**
     * @inheritdoc
     */
    protected function configure() {
      $this->setName('fix');

      $this->setDescription('Fix code according your code style');
      $this->addOption('save', null, InputOption::VALUE_NONE, 'By default we will show info without code modification');
      parent::configure();
    }


    /**
     * @inheritdoc
     */
    protected function getResultState(InputInterface $input, OutputInterface $output, Report $report) {
      $isVerbose = ($output->getVerbosity() === OutputInterface::VERBOSITY_DEBUG);

      if ($report->count() === 0) {
        $output->writeln('<info>✔ Looking good</info>');
        return 0;
      }

      $output->writeln('');

      foreach ($report as $message) {
        $output->write('<info>');
        $output->writeln('file    : ' . $message->getFile()->getPath() . ':' . $message->getLine());
        $output->writeln('tool    : ' . $message->getTool()->getName());
        if ($isVerbose) {
          $output->writeln('info    : ' . $message->getTool()->getDescription());
        }

        $output->writeln('message : ' . $message->getText());
        $output->writeln('</info>');
      }

      if ($input->getOption('save')) {
        $output->writeln('<info>✔ Fixed</info>');
      } else {
        $output->writeln('<comment>✔ Dry run</comment>');
      }
      return 0;
    }


    /**
     * @inheritdoc
     */
    protected function getFileProcessor(InputInterface $input, OutputInterface $output) {
      $fixer = new FixerProcessor();
      $fixer->setSaveFiles($input->getOption('save'));
      return $fixer;
    }


    /**
     * @return ConfigurationInterface
     */
    protected function getDefaultConfiguration() {
      return CsConfiguration::createFixerConfiguration();
    }

  }
