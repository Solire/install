<?php
namespace Solire\Install\Lib;

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
        $config = $this->askIni($defaultPath, $sections, $hiddenAnswers);
        Ini::write($newPath, $config);
    }

    /**
     * Read an ini-file, an foreach sections (if defined) or all sections,
     * for each line the value will be prompt.
     *
     * You can define for each sections some keys who's answer better be hidden,
     * like the databases password.
     *
     * @param string $iniPath       The ini-file to read
     * @param array  $sections      The section to question
     * @param array  $hiddenAnswers An associative array where the keys are a
     * section name and the value are a list of key where the answer will be
     * hidden
     *
     * @return array
     */
    public function askIni(
        $iniPath,
        $sections = null,
        $hiddenAnswers = null
    ) {
        /**
         * Use Solire\Lib\Config instead
         */
        $config = Ini::parse($iniPath);

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
                $iniPath
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

        return $config;
    }

}
