<?php
namespace Dime\Server\Config;

use Dime\Server\Exception\FileNotFoundException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

/**
 * Loader - Yaml configuration loader.
 *
 * @author Danilo Kühn <dk@nogo-software.de>
 * @package Config
 */
class Loader implements \ArrayAccess, \Countable
{
    /**
     * @var array
     */
    protected $configFiles = [];

    /**
     * @var array
     */
    protected $config = [];

    public function count($mode = COUNT_NORMAL)
    {
        return count($this->config, $mode);
    }

    public function offsetExists($offset)
    {
        return isset($this->config[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->config[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->config[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->config[$offset]);
    }

    /**
     * Load more than one file into config.
     * 
     * @param *args files to load
     * @return Loader
     * @throws ParseException
     * @throws FileNotFoundException
     */
    public function import()
    {
        if (func_num_args() > 0) {
            foreach (func_get_args() as $file) {
                $this->load($file, dirname($file));
            }
        }
        return $this;
    }

    /**
     * Merge array into config array.
     *
     * @param array $config array to merge
     * @param bool $first true, this config will be first, parameter array will be second
     */
    public function merge(array $config)
    {
        $this->config = $this->resolve($this->array_merge_recursive_distinct($this->config, $config));
    }

    /**
     * Resolve configuration paths with %name_dir%
     *
     * @param array $config
     * @return array
     */
    public function resolve(array $config = null)
    {
        if ($config === null) {
            $config = $this->config;
        }
        array_walk_recursive($config, [$this, 'resolver'], $config);
        return $config;
    }

    /**
     * Load file.
     * 
     * @param string $file
     * @param string $path
     * @throws ParseException
     * @throws FileNotFoundException
     */
    protected function load($file, $path =  '')
    {
        $file = $this->relativeTo($file, $path);
        if (file_exists($file)) {
            $hash = md5($file);
            if (!isset($this->configFiles[$hash])) {
                $this->configFiles[$hash] = $file;

                $values = Yaml::parse($file);
                $this->merge($values);
                $this->loadImport($path);
            }
        } else {
            throw new FileNotFoundException('File [' . $file . '] not found.');
        }
    }

    /**
     * Load import part of configuration. Configuration array should have
     * 'import' key.
     *
     * @throws ParseException
     * @throws FileNotFoundException
     */
    protected function loadImport($path)
    {
        if (isset($this->config['import'])) {
            $import = $this->config['import'];
            unset($this->config['import']);
            foreach ($import as $value) {
                $this->load($value, $path);
            }
        }
    }

    /**
     * Resolve paths
     * 
     * @param string $item
     * @param string $key
     * @param array $data
     */
    protected function resolver(&$item, $key, $data)
    {
        $matches = [];
        if (preg_match('/%(.*_dir)%/', $item, $matches)) {
            $constName = strtoupper($matches[1]);
            if (defined($constName)) {
                $value = constant($constName);
                $item = str_replace('%' . $matches[1] . '%', $value, $item);
            } else if (array_key_exists($matches[1], $data)) {
                $item = str_replace('%' . $matches[1] . '%', $data[$matches[1]], $item);
            }
        }
    }

    protected function relativeTo($file, $path)
    {
        $result = $file;
        if (strpos($file, DIRECTORY_SEPARATOR) !== 0) {
            $result = $path . DIRECTORY_SEPARATOR . $file;
        }
        return $result;
    }

    /**
     * Merge array recursive and overwrite keys.
     *
     * @param array $array1
     * @param array $array2
     * @return type
     */
    protected function array_merge_recursive_distinct(array &$array1, array &$array2)
    {
        $merged = $array1;

        foreach ($array2 as $key => &$value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = $this->array_merge_recursive_distinct($merged[$key], $value);
            } else if (is_numeric($key)) {
                $merged[] = $value;
            } else {
                $merged[$key] = $value;
            }
        }

        return $merged;
    }

}
