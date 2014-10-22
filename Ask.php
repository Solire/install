<?php
/**
 * Prompts questions relative to ini-files
 *
 * @author  thansen <thansen@solire.fr>
 * @license CC by-nc http://creativecommons.org/licenses/by-nc/3.0/fr/
 */

namespace Solire\Install;

use Composer\Script\Event;

/**
 * Prompts questions relative to ini-files
 *
 * @author  thansen <thansen@solire.fr>
 * @license CC by-nc http://creativecommons.org/licenses/by-nc/3.0/fr/
 */
class Ask
{
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
        $extras = $event->getComposer()->getPackage()->getExtra();
        $filesPath = $extras['solire']['parameters'];
        $parameters = new Parameters($event->getIO());
        foreach ($filesPath as $filePath => $moreParams) {
            $sections = null;
            if (isset($moreParams['sections'])) {
                $sections = $moreParams['sections'];
            }

            $hiddenAnswer = null;
            if (isset($moreParams['hideAnswer'])) {
                $hiddenAnswer = $moreParams['hideAnswer'];
            }

            $defaultFile = $filePath . '.default.ini';
            $newFile = $filePath . '.ini';

            if (!file_exists($newFile)
                && file_exists($defaultFile)
            ) {
                $parameters->processFile(
                    $defaultFile,
                    $newFile,
                    $sections,
                    $hiddenAnswer
                );
            }
        }
    }
}
