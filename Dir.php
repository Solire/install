<?php

namespace Solire\Install;

use Composer\Script\Event;

/**
 * Manage directories.
 *
 * @author thansen
 */
class Dir
{
    /**
     * Set the writing right for all on twig cache directory.
     *
     * @param Event $event The composer event
     *
     * @return void
     */
    public static function allowPermissions(Event $event)
    {
        $extra = $event->getComposer()->getPackage()->getExtra();
        $dirs = $extra['solire']['allowPermissions'];

        foreach ($dirs as $dir) {
            chmod(realpath($dir), 0777);

            $msg = sprintf(
                '<info>Toutes permissions données sur le dossier "%s"</info>',
                realpath($dir)
            );
            $event->getIO()->write($msg);
        }
    }
}
