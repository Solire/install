<?php
namespace Solire\Install;

use Composer\Script\Event;
use Solire\Conf\Loader as ConfLoader;
use Solire\Install\Lib\Symlink;
use Solire\Lib\Path;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

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
        Symlink::createDir('public');

        $extra = $event->getComposer()->getPackage()->getExtra();

        $conf = ConfLoader::load($extra['solire']['frontEnd']['dirs']);

        foreach ($conf->dirs as $linkDir => $targetName) {
            $targetDirPath = new Path(
                sprintf($conf->targetMask, $targetName),
                Path::SILENT
            );

            if ($targetDirPath->get() === false) {
                continue;
            }

            $targetDir = $targetDirPath->get();

            $finder = Finder::create()
                ->in($targetDir)
                ->depth(0)
                ->directories()
            ;

            /* @var $target SplFileInfo */
            foreach ($finder as $target) {
                /*
                 * $dirName = js|css|img|font|...
                 */
                $dirName = $target->getBasename();
                $linkPath = sprintf($conf->linkMask, $linkDir, $dirName);
                $link = new Symlink($target->getRealPath(), $linkPath);
                if ($link->create()) {
                    $msg = sprintf(
                        '<info>Création d\'un lien "%s" vers le dossier "%s"</info>',
                        $linkPath,
                        $target->getRealPath()
                    );
                    $event->getIO()->write($msg);
                }
            }
        }
    }
}
