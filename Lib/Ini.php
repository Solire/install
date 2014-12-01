<?php
namespace Solire\Install\Lib;

use Solire\Lib\Config;

/**
 * A parser and builder of ini files
 *
 * @author  thansen <thansen@solire.fr>
 * @license CC by-nc http://creativecommons.org/licenses/by-nc/3.0/fr/
 */
class Ini
{
    /**
     * Parse a configuration file
     *
     * @param string $path The path of the ini file being parsed.
     *
     * @return array
     */
    public static function parse($path)
    {
        return parse_ini_file($path, true);
    }

    /**
     * Write a configuration file from an associative array
     *
     * @param string $path
     * @param array $config Configuration array
     *
     * @return bool
     */
    public static function write($path, array $config)
    {
        $content = self::build($config);
        return (bool) file_put_contents($path, $content);
    }

    /**
     * Create te content of an ini-file from an array (reverse fonction to
     * parse_ini_file, but does not write in a file)
     *
     * @param array $config An associative array (correspond to an ini
     * structured file)
     *
     * @return string the content of an ini-file
     * @see parse_ini_file
     */
    public static function build(array $config)
    {
        $iniContent = '';

        foreach ($config as $section => $list) {
            $iniContent .= self::buildIniSection($section) . "\n";

            foreach ($list as $key => $value) {
                $iniContent .= self::buildIniLine($key, $value) . "\n";
            }

            $iniContent .= "\n";
        }

        return $iniContent;
    }

    /**
     * Return the line corresponding to a section in an ini-file
     *
     * @param type $section The name of the section
     *
     * @return string
     */
    private static function buildIniSection($section)
    {
        return '[' . $section . ']';
    }

    /**
     * Convert a key - value couple into an ini Line
     *
     * @param string $key   The key
     * @param string $value The value
     *
     * @return string
     */
    private static function buildIniLine($key, $value)
    {
        return $key . ' = ' . self::buildIniValue($value);
    }

    /**
     * Wrap a value inside enclosure (" or ')
     *
     * @param string $value The value
     *
     * @return string
     * @throws \Exception in case of wrong ini value
     */
    private static function buildIniValue($value)
    {
        $enclosure = '"';
        if (strpos($value, $enclosure) !== false) {
            $enclosure = '\'';
            if (strpos($value, $enclosure) !== false) {
                throw new \Exception(
                    'Ini can\'t contain both " and \' character!'
                );
            }
        }

        return $enclosure . $value . $enclosure;
    }
}
