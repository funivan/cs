<?php

  namespace Funivan\Cs\Tools\Php\RedundantNullPropertyValue;

  use Funivan\Cs\FileTool\FileTool;
  use Funivan\Cs\Fs\File;
  use Funivan\Cs\Fs\FileFilter;
  use Funivan\Cs\Report\Report;
  use Funivan\PhpTokenizer\Collection;

  /**
   *
   */
  class RedundantNullPropertyValueFixer implements FileTool {


    /**
     * Return unique string of the tool
     * You can set any name but we suggest to use following rules:
     *  - Allowed chars [a-z0-9_]+
     *  - Review tools should have ending `_review`
     *  - Fixer tools should have ending `_fixer`
     *
     * @codeCoverageIgnore
     * @return string
     */
    public function getName() {
      return 'redundant_null_property_value_fixer';
    }


    /**
     * @return string
     */
    public function getDescription() {
      return 'Replace null assignment from the class property';
    }


    /**
     * Check if we can process file by this tool
     * Called before file process
     *
     * @param File $file
     * @return boolean
     */
    public function canProcess(File $file) {
      return (new FileFilter())->notDeleted()->extension(['php'])->isValid($file);
    }


    /**
     * @param File $file
     * @param Report $report
     */
    public function process(File $file, Report $report) {
      $collection = Collection::createFromString($file->getContent()->get());


      $invalidProperties = InvalidPropertyFinder::find($collection);
      if (count($invalidProperties) === 0) {
        return;
      }

      foreach ($invalidProperties as $property) {
        $report->addMessage($file, $this, 'Replace redundant NULL value', $property->getVariable()->getLine());
        foreach ($property->getTokensToReplace() as $token) {
          $token->remove();
        }
      }

      $file->getContent()->set($collection->assemble());
    }

  }