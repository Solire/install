<?php
namespace Solire\Install;

use Composer\Script\Event;
use Solire\Conf\Loader as ConfLoader;
use Solire\Lib\Path;

/**
 * Prompts questions relative to ini-files
 *
 * @author  thansen <thansen@solire.fr>
 * @license CC by-nc http://creativecommons.org/licenses/by-nc/3.0/fr/
 */
class Bower
{
    const BOWER_SCRIPT = 'bin/bowerUpdate';

    /**
     * A composer installation script to make symlinks
     *
     * @param Event $event The composer event
     *
     * @return void
     */
    public static function install(Event $event)
    {
        $extra = $event->getComposer()->getPackage()->getExtra();

        $symlinksConfig = ConfLoader::load($extra['frontEnd']['dirs']);

        $cmd = __DIR__ . Path::DS . self::BOWER_SCRIPT . ' %s';

        foreach ($symlinksConfig->dirs as $linkDir => $targetDir) {
            $targetDir = $targetDir;
            $targetDirPath = new Path($targetDir, Path::SILENT);

            if ($targetDirPath->get() === false) {
                continue;
            }

            $targetDir = $targetDirPath->get();

            $output = shell_exec(sprintf($cmd, $targetDir));
            $event->getIO()->write($output);
        }
    }
}
