<?php

namespace Solire\Install;

use Composer\Script\Event;
use Solire\Conf\Loader as ConfLoader;
use Solire\Lib\Path;

/**
 * Install front end tools.
 *
 * @author  thansen <thansen@solire.fr>
 * @license CC by-nc http://creativecommons.org/licenses/by-nc/3.0/fr/
 */
class FrontEnd
{
    const SCRIPT = 'bin/frontEnd';

    /**
     * A composer installation script to make symlinks.
     *
     * @param Event $event The composer event
     *
     * @return void
     */
    public static function install(Event $event)
    {
        $extra = $event->getComposer()->getPackage()->getExtra();

        $conf = ConfLoader::load($extra['solire']['frontEnd']['dirs']);

        $cmd = __DIR__ . Path::DS . self::SCRIPT . ' %s';

        foreach ($conf->dirs as $targetName) {
            $targetDirPath = new Path(
                $targetName,
                Path::SILENT
            );

            if ($targetDirPath->get() === false) {
                continue;
            }

            $targetDir = $targetDirPath->get();

            $msg = sprintf(
                '<info>Installation du Front End "%s"</info>',
                $targetDir
            );
            $event->getIO()->write($msg);
            $output = shell_exec(sprintf($cmd, $targetDir));
            $event->getIO()->write($output);
        }
    }
}
