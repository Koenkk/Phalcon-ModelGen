<?php

namespace BasilFX\ModelGen;

/**
 * Wrapper for the compiler-as-stream option.
 */
class Stream
{
    /**
     * @var array Mapping of stream to compiler. Each compiler will register
     *            itself to this array.
     */
    public static $mappings = [];

    /**
     * @var string Path to compiled file.
     */
    private $compiledPath;

    /**
     * @var resource Pointer to open file (for wrapping fopen/fread calls).
     */
    public $context;

    /**
     * @param $path
     */
    private function compile($path)
    {
        $prefix = substr($path, 0, strpos($path, "://"));
        $sourceFile = substr($path, strlen($prefix . "://"));

        $this->compiledPath = self::$mappings[$prefix]->compile($sourceFile);
    }
    /**
     * @param $path
     * @param $flags
     */
    public function url_stat($path, $flags)
    {
        if (!$this->compiledPath) {
            $this->compile($path);
        }

        return stat($this->compiledPath);
    }

    /**
     * @param $path
     * @param $mode
     * @param $options
     * @param $openedPath
     */
    public function stream_open($path, $mode, $options, &$openedPath)
    {
        if (!$this->compiledPath) {
            $this->compile($path);
        }

        $this->context = fopen($this->compiledPath, "r");

        return $this->context !== false;
    }

    public function stream_close()
    {
        fclose($this->context);
    }

    /**
     * @param $count
     */
    public function stream_read($count)
    {
        return fread($this->context, $count);
    }

    public function stream_stat()
    {
        return fstat($this->context);
    }

    public function stream_eof()
    {
        return feof($this->context);
    }

    public function stream_set_option(int $option, int $arg1, int $arg2)
    {
        switch ($option) {
            case STREAM_OPTION_BLOCKING:
                return stream_set_blocking($this->context, $arg1);
            case STREAM_OPTION_READ_TIMEOUT:
                return stream_set_timeout($this->context, $arg1, $arg2);
            case STREAM_OPTION_WRITE_BUFFER:
                return stream_set_write_buffer($this->context, $arg2);
            default:
                return false;
        }
    }
}
