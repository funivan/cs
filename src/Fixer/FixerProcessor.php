<?php

  namespace Funivan\Cs\Fixer;

  use Funivan\Cs\FileFinder\FileInfoCollection;
  use Funivan\Cs\FileProcessor\BaseFileProcessor;
  use Funivan\Cs\Message\Report;

  /**
   
   * @author Ivan Shcherbak <dev@funivan.com> 2016
   */
  class FixerProcessor extends BaseFileProcessor {

    /**
     * @var bool
     */
    private $saveFiles = false;


    /**
     * @param FileInfoCollection $files
     * @param Report $report
     */
    public function process(FileInfoCollection $files, Report $report) {

      if (empty($this->getTools())) {
        $this->getOutput()->writeln('<comment>Empty tools list</comment>');
        return;
      }

      $message = '✘ dry run  ';
      if ($this->saveFiles) {
        $message = '✔ fixed    ';
      }

      foreach ($files as $file) {

        $fixed = false;

        foreach ($this->getTools() as $tool) {

          if ($this->getOutput()->isDebug()) {
            $this->getOutput()->writeln('process : (' . $tool->getName() . ') ' . $file->getPath());
          }

          if (!$tool->canProcess($file)) {
            continue;
          }

          $tool->process($file, $report);

          $tokenizer = $file->getTokenizer();
          $fileChanged = $tokenizer->isChanged();
          if ($fileChanged === false) {
            continue;
          }

          $tokenizer->refresh();
          $fixed = true;

          $filePathInfo = '';
          if ($this->saveFiles === false) {
            $filePathInfo = ' ## ' . $file->getPath();
          }

          $this->getOutput()->writeln('<info>' . $message . ': ' . $tool->getDescription() . $filePathInfo . '</info>');
        }

        if ($fixed and $this->saveFiles) {
          $file->getTokenizer()->save();
          $this->getOutput()->writeln('<info>' . '✔ saved    : ' . $file->getPath() . '</info>');
        }

      }

    }


    /**
     * @return boolean
     */
    public function getSaveFiles() {
      return $this->saveFiles;
    }


    /**
     * @param boolean $saveFiles
     * @return $this
     */
    public function setSaveFiles($saveFiles) {
      $this->saveFiles = $saveFiles;
      return $this;
    }

  }