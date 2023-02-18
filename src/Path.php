<?php

final class Path {

    public static function join(string ...$segments) : string
    {
        return implode(DIRECTORY_SEPARATOR, $segments);
    }

    /**
     *
     * @param $ignore array This is an array of regexps to match against path names. The forward slash `/` does not need to be escaped
     * For example:
     * ```
     * scan_for_files(ignore: ['/vendor/?.*', '/tests'])
     * ```
     * @return SplFileInfo[]
     */
    public static function scan_for_files(string $directory, string $file_regexp, array $ignore = []): array
    {
        $dit = new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS);
        $mdit = static::getFileSystemIteratorFilter($dit, $ignore);

        $it = new RegexIterator(new RecursiveIteratorIterator($mdit), "/$file_regexp/");
        return iterator_to_array($it);
    }

    public static function getFileSystemIteratorFilter(RecursiveIterator $iterator, array $ignore = []) {
        return new FileSystemIteratorFilter($iterator, $ignore, true);
    }

}

final class FileSystemIteratorFilter extends RecursiveFilterIterator
{
    private readonly string $ignore_pattern;

    public function __construct(
        RecursiveIterator $iterator,
        private readonly array $ignore = []
    ) {
        $this->ignore_pattern = "/(" . join(
            "|",
            array_map(fn ($i) => str_replace("/", '\/', $i), $ignore)
        ) . ")/";
        parent::__construct($iterator);
    }

    public function accept(): bool
    {
        if (empty($this->ignore)) return true;

        $path = str_replace("\\", "/", realpath($this->getFilename()));
        return !@preg_match($this->ignore_pattern, $path);
    }

    public function getChildren(): ?RecursiveFilterIterator
    {
        return new FileSystemIteratorFilter($this->getInnerIterator()->getChildren(), $this->ignore);
    }
}