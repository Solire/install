<?php
/**
 * Prompt parameters from a list of ini-files
 *
 * @author  thansen <thansen@solire.fr>
 * @license CC by-nc http://creativecommons.org/licenses/by-nc/3.0/fr/
 */

namespace Solire\Install;

use Composer\IO\IOInterface;

/**
 * Prompt parameters from a list of ini-files
 *
 * @author  thansen <thansen@solire.fr>
 * @license CC by-nc http://creativecommons.org/licenses/by-nc/3.0/fr/
 */
class Parameters
{
    /**
     * Input / Output interface
     *
     * @var Composer\IO\IOInterface
     */
    private $io;

    /**
     * Constructor
     *
     * @param IOInterface $io The input output interface
     */
    public function __construct(IOInterface $io)
    {
        $this->io = $io;
    }

    /**
     * Read an ini-file, an foreach sections (if defined) or all sections,
     * for each line the value will be prompt.
     *
     * You can define for each sections some keys who's answer better be hidden,
     * like the databases password.
     *
     * The values entered are written in a new ini-file
     *
     * @param string $defaultPath   The ini-file to read
     * @param string $newPath       The ini-file to write
     * @param array  $sections      The section to question
     * @param array  $hiddenAnswers An associative array where the keys are a
     * section name and the value are a list of key where the answer will be
     * hidden
     *
     * @return void
     */
    public function processFile(
        $defaultPath,
        $newPath,
        $sections = null,
        $hiddenAnswers = null
    ) {
        /**
         * Use Solire\Lib\Config instead
         */
        $config = parse_ini_file($defaultPath, true);

        foreach ($config as $section => $list) {
            $hiddenAnswer = [];
            if ($hiddenAnswers !== null
                && isset($hiddenAnswers[$section])
            ) {
                $hiddenAnswer = $hiddenAnswers[$section];
            }

            if ($section !== null && !in_array($section, $sections)) {
                continue;
            }

            $m = sprintf(
                '<info>there\'s this section named %s in the "%s" file</info>',
                $section,
                $defaultPath
            );
            $this->io->write($m);

            foreach ($list as $key => $default) {
                if (in_array($key, $hiddenAnswer)) {
                    /*
                     * If a secret is asked, no display of the answer
                     */

                    $q = sprintf(
                        '<question>%s</question>: ',
                        $key
                    );
                    $value = $this->io->askAndHideAnswer($q);
                } else {
                    /*
                     * If there's no problem in showing the answer
                     */

                    $q = sprintf(
                        '<question>%s</question> (<comment>%s</comment>): ',
                        $key,
                        $default
                    );
                    $value = $this->io->ask($q, $default);
                }

                $config[$section][$key] = $value;
            }
        }

        $newContent = $this->buildIni($config);
        file_put_contents($newPath, $newContent);
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
    public function buildIni($config)
    {
        $iniContent = '';

        foreach ($config as $section => $list) {
            $iniContent .= $this->buildIniSection($section) . "\n";

            foreach ($list as $key => $value) {
                $iniContent .= $this->buildIniLine($key, $value) . "\n";
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
    private function buildIniSection($section)
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
    private function buildIniLine($key, $value)
    {
        return $key . ' = ' . $this->buildIniValue($value);
    }

    /**
     * Wrap a value inside enclosure (" or ')
     *
     * @param string $value The value
     *
     * @return string
     * @throws \Exception in case of wrong ini value
     */
    private function buildIniValue($value)
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
