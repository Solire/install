<?php
namespace Solire\Install;

use Composer\Script\Event;
use Solire\Lib\Path;
use Symfony\Component\Finder\Finder;
use Exception;

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
//        self::createDir('public/front');
//        self::createDir('public/back');

        $appDirs = [
            'front' => 'default/front',
            'back' => 'default/back',

            'vel/Front' => 'vel/front',
            'vel/Back' => 'vel/back',

            'client/Front' => 'client/front',
            'client/Back' => 'client/back',
        ];

        foreach ($appDirs as $targetDir => $linkDir) {
            $targetDirPath = new Path($targetDir, Path::SILENT);

            if ($targetDirPath->get() === false) {
                continue;
            }

            $finder = Finder::create()
                ->in('vendor/solire/' . $targetDir)
                ->depth(0)
                ->directories()
            ;

            /* @var $target \Symfony\Component\Finder\SplFileInfo */
            foreach ($finder as $target) {
                /*
                 * $dirName = js|css|img|font|...
                 */
                $dirName = $target->getBasename();

                self::createDir('public/' . $linkDir);

                $link = 'public/' . $linkDir . '/' . $dirName;
                self::link($target->getRealPath(), $link);
            }
        }
    }

    /**
     *
     *
     * @param string $target
     * @param string $link
     * @param bool   $force
     *
     * @return bool
     * @throws Exception
     */
    protected static function link($target, $link, $force = true)
    {
        $targetPath = new Path($target, Path::SILENT);
        if ($targetPath->get() === false) {
            return false;
        }
        $target = $targetPath->get();

        $linkPath = new Path($link, Path::SILENT);
        if ($linkPath->get() !== false) {
            $link = $linkPath->get();

            if (!$force) {
                return false;
            }

            if (is_link($link)) {
                unlink($link);
            } else {
                throw new Exception('"' . $link . '" existe déjà et n\'est pas '
                    . 'un lien symbolique'
                );
            }
        } else {
            $parent = pathinfo($link, PATHINFO_DIRNAME);
            $parentPath = new Path($parent);
            $link = $parentPath->get() . '/' . pathinfo($link, PATHINFO_BASENAME);
        }

        $status = symlink($target, $link);
        if (!$status) {
            throw new Exception('La création du lien "' . $link . '" vers "'
                . $target . '" a échouée'
            );
        }

        return true;
    }

    /**
     * Créer un dossier en vérifiant au préalable s'il existe déjà
     *
     * @param string $path  Le chemin du dossier
     *
     * @return bool
     * @throws Exception Si le fichier existe déjà mais n'est pas un dossier
     */
    protected static function createDir($path)
    {
        if (file_exists($path)
            && is_dir($path)
        ) {
            return true;
        }

        if (file_exists($path)) {
            throw new Exception('On ne peut pas créer le dossier '
                . '"' . $path . '" car il existe déjà mais n\'est pas un'
                . 'dossier'
            );
        }

        return mkdir($path, 0777, true);
    }
}
