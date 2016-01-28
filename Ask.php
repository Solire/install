<?php
namespace Solire\Install;

use Composer\IO\IOInterface;
use Composer\Script\Event;
use Exception;

/**
 * Prompts questions
 *
 * @author  thansen <thansen@solire.fr>
 * @license CC by-nc http://creativecommons.org/licenses/by-nc/3.0/fr/
 */
class Ask
{
    private static $alias = [];

    /**
     * Initialisation
     *
     * @param Event $event The composer event
     *
     * @return void
     */
    public static function init(Event $event)
    {
        $extra = $event->getComposer()->getPackage()->getExtra();

        $parameters = [];
        if (isset($extra['solire']['parameters'])) {
            $parameters = $extra['solire']['parameters'];
        }

        foreach ($parameters as $path => $data) {
            $parameters[$path] = array_merge(
                yaml_parse_file($path),
                $data
            );
        }

        $extra['solire']['parameters'] = $parameters;
        $event->getComposer()->getPackage()->setExtra($extra);
    }

    /**
     * A composer installation script to define the different configuration
     * parameters
     *
     * @param Event $event The composer event
     *
     * @return void
     */
    public static function parameters(Event $event)
    {
        $io = $event->getIO();

        $extra = $event->getComposer()->getPackage()->getExtra();

        $parameters = [];
        if (isset($extra['solire']['parameters'])) {
            $parameters = $extra['solire']['parameters'];
        }

        $configs = yaml_parse_file($extra['solire']['ask']);

        foreach ($configs as $path => $questions) {
            if (!isset($parameters[$path])) {
                $parameters[$path] = [];
            }

            foreach ($questions as $section => $sectionQuestions) {
                if (!isset($parameters[$path][$section])) {
                    $parameters[$path][$section] = [];
                }

                foreach ($sectionQuestions as $key => $question) {
                    $default = null;
                    if (isset($parameters[$path][$section][$key])) {
                        $default = $parameters[$path][$section][$key];
                    }

                    $parameters[$path][$section][$key] = self::askQuestion(
                        $io,
                        $question,
                        $default
                    );
                }
            }
        }

        $extra['solire']['parameters'] = $parameters;
        $event->getComposer()->getPackage()->setExtra($extra);
    }

    /**
     * Pose une question
     *
     * @param IOInterface $io       The input output interface
     * @param array       $question The question config
     * @param string      $default  The default value
     *
     * @return string
     */
    private static function askQuestion(
        IOInterface $io,
        array $question,
        $default = null
    ) {
        if (!empty($question['copy'])) {
            return self::copyFromAlias($question['copy']);
        }

        if ($default === null && isset($question['default'])) {
            $default = $question['default'];
        }

        $text = sprintf(
            '<question>%s</question> (<comment>%s</comment>): ',
            $question['text'],
            $default
        );

        if (!empty($question['hide'])) {
            $answer = $io->askAndHideAnswer($text, $default);
        } else {
            $answer = $io->ask($text, $default);
        }
        if (!empty($question['alias'])) {
            self::$alias[$question['alias']] = $answer;
        }

        return $answer;
    }

    private static function saveAlias($alias, $answer)
    {
        self::$alias[$alias] = $answer;
    }

    /**
     * Copie une valeurs avec un alias
     *
     * @param string $alias Alias
     *
     * @return string
     * @throws Exception Si un alias n'existe pas
     */
    private static function copyFromAlias($alias)
    {
        if (!isset(self::$alias[$alias])) {
            throw new Exception(
                sprintf(
                    'parameters alias [%s] does not exist',
                    $alias
                )
            );
        }

        return self::$alias[$alias];
    }

    /**
     * Write config
     *
     * @param Event $event The composer event
     *
     * @return void
     */
    public static function write(Event $event)
    {
        $extra = $event->getComposer()->getPackage()->getExtra();
        $parameters = $extra['solire']['parameters'];

        unset ($parameters['temp']);

        foreach ($parameters as $path => $data) {
            yaml_emit_file($path, $data);
        }
    }
}
