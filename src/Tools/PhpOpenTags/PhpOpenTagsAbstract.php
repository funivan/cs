<?php

  namespace Funivan\Cs\Tools\PhpOpenTags;

  use Funivan\Cs\FileFinder\FileInfo;
  use Funivan\Cs\FileProcessor\CanProcessHelper;
  use Funivan\Cs\FileProcessor\FileTool;
  use Funivan\PhpTokenizer\Query\Query;

  /**
   *
   */
  abstract class PhpOpenTagsAbstract implements FileTool {

    /**
     * @var null
     */
    private $tagFormat = null;


    /**
     * @param int|null $tagFormat
     */
    public function __construct($tagFormat = null) {
      if (!PhpOpenTagsConfiguration::isValidTagFormat($tagFormat)) {
        throw new \InvalidArgumentException('Invalid tag format');
      }

      $this->tagFormat = $tagFormat;
    }


    /**
     * @return string
     */
    public function getDescription() {
      $type = $this->useFullTags() ? 'full' : 'short';
      return 'Use only ' . $type . ' php tags according to your code style';
    }


    /**
     * @param FileInfo $file
     * @return boolean
     */
    public function canProcess(FileInfo $file) {
      return (new CanProcessHelper())->notDeleted()->extension(['php', 'html'])->isValid($file);
    }


    /**
     * @return bool
     */
    protected function useFullTags() {
      return $this->tagFormat === PhpOpenTagsConfiguration::TAG_FORMAT_FULL;
    }


    /**
     * @return bool
     */
    protected function useShortTags() {
      return $this->tagFormat === PhpOpenTagsConfiguration::TAG_FORMAT_SHORT;
    }


    /**
     * @param FileInfo $file
     * @return \Funivan\PhpTokenizer\Collection
     */
    protected function findTags(FileInfo $file) {
      $query = (new Query());
      $query->typeIs(T_OPEN_TAG);
      if ($this->useShortTags()) {
        $query->valueLike('!^<\?php\s+!'); // long php tag contains also spaces
      } else {
        $query->valueIs('<?');
      }

      $tokens = $file->getTokenizer()->getCollection();


      return $tokens->find($query);
    }


  }