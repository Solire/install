<?php
namespace Solire\Install;

use Composer\Script\Event;

/**
 * Prompts questions
 *
 * @author  thansen <thansen@solire.fr>
 * @license CC by-nc http://creativecommons.org/licenses/by-nc/3.0/fr/
 */
class Ask
{
    /**
     *
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
            $parameters[$path] = array_merge(yaml_parse_file($path), $parameters[$path]);
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
        $extra = $event->getComposer()->getPackage()->getExtra();

        $parameters = [];
        if (isset($extra['solire']['parameters'])) {
            $parameters = $extra['solire']['parameters'];
        }

        $configs = yaml_parse_file($extra['solire']['ask']);

        foreach ($configs as $path => $questions) {
            foreach ($questions as $section => $sectionQuestions) {
                foreach ($sectionQuestions as $key => $question) {
                    $default = $parameters[$path][$section][$key];
                    if (isset($question['default'])) {
                        $default = $question['default'];
                    }

                    $text = sprintf(
                        '<question>%s</question> (<comment>%s</comment>): ',
                        $question['text'],
                        $default
                    );

                    if (isset($question['hide']) && $question['hide']) {
                        $answer = $event->getIO()->askAndHideAnswer($text, $default);
                    } else {
                        $answer = $event->getIO()->ask($text, $default);
                    }

                    $parameters[$path][$section][$key] = $answer;
                }
            }
        }

        $extra['solire']['parameters'] = $parameters;
        $event->getComposer()->getPackage()->setExtra($extra);
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

        foreach ($parameters as $path => $data) {
            yaml_emit_file($path, $data);
        }
    }
}
