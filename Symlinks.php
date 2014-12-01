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
        self::createDir('public');
        self::createDir('public/front');
        self::createDir('public/back');

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

    /**
     * Create a directory and checks if already existing
     *
     * @param string $path  The path to the directory
     * @param bool   $force If a file exists with the same name, if "true",
     * unlink it and continue else return false
     *
     * @return bool
     */
    protected static function createDir($path, $force = true)
    {
        if (file_exists($path)
            && is_dir($path)
        ) {
            return true;
        }

        if (file_exists($path)) {
            if ($force) {
                unlink($path);
            } else {
                return false;
            }
        }

        return mkdir($path);
    }
}
