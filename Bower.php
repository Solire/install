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

        $conf = ConfLoader::load($extra['solire']['frontEnd']['dirs']);

        $cmd = __DIR__ . Path::DS . self::BOWER_SCRIPT . ' %s';

        foreach ($conf->dirs as $targetName) {
            $targetDirPath = new Path(
                sprintf($conf->targetMask, $targetName),
                Path::SILENT
            );

            if ($targetDirPath->get() === false) {
                continue;
            }

            $targetDir = $targetDirPath->get();

            $msg = sprintf(
                '<info>Installation de bower "%s"</info>',
                $targetDir
            );
            $output = shell_exec(sprintf($cmd, $targetDir));
            $event->getIO()->write($output);
        }
    }
}
