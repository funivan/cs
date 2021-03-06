<?php

  namespace Funivan\Cs\Tools\LineEnding;

  use Funivan\Cs\Fs\File;
  use Funivan\Cs\Report\Report;

  /**
   * @author Ivan Shcherbak <dev@funivan.com> 2016
   */
  class LineEndingReview extends LineEndingAbstract {

    const NAME = 'line_ending_review';


    /**
     * @inheritdoc
     */
    public function getName() {
      return self::NAME;
    }


    /**
     * @inheritdoc
     */
    public function process(File $file, Report $report) {
      $collection = \Funivan\PhpTokenizer\Collection::createFromString($file->getContent()->get());
      $tokens = $collection->find($this->getFindQuery());

      foreach ($tokens as $token) {
        $report->addMessage($file, $this, 'Expect only LF line ending', $token->getLine());
      }

    }

  }