<?php
namespace Solire\Install;

use Composer\Script\Event;

/**
 * Prompts questions relative to ini-files
 *
 * @author  thansen <thansen@solire.fr>
 * @license CC by-nc http://creativecommons.org/licenses/by-nc/3.0/fr/
 */
class Symlinks
{
    /**
     * A composer installation script to make symlinks
     *
     * @param Event $event The composer event
     *
     * @return void
     */
    public static function create(Event $event)
    {
        $appDirs = [
            'front' => 'public/front/default',
            'back' => 'public/back/default',

            'vel/Front' => 'public/front/vel',
            'vel/Back' => 'public/back/vel',

            'client/Front' => 'public/front/client',
            'client/Back' => 'public/back/client',
        ];
        foreach ($appDirs as $targetDir => $linkDir) {
            $target = realpath('vendor/solire/' . $targetDir);
            if (!file_exists($target)) {
                continue;
            }
            if (file_exists($linkDir)) {
                if (is_link($linkDir)) {
                    unlink($linkDir);
                } else {
                    continue;
                }
            }
            $link = realpath($linkDir);
            symlink($target, $link);
        }
    }
}
