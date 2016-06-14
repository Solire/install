<?php

namespace Solire\Install\Lib;

use Exception;
use Solire\Lib\Path;

/**
 * Description of Symlink.
 *
 * @author thansen
 */
class Symlink
{
    /**
     * Chemin de la cible.
     *
     * @var string
     */
    private $target;

    /**
     * Chemin du lien.
     *
     * @var string
     */
    private $link;

    /**
     * Supprime le lien s'il existe déjà.
     *
     * @var bool
     */
    private $force;

    /**
     * Constructeur.
     *
     * @param string $target Chemin de la cible
     * @param string $link   Chemin du lien
     * @param bool   $force  Supprime le lien s'il existe déjà
     */
    public function __construct($target, $link, $force = true)
    {
        $this->target = $target;
        $this->link = $link;
        $this->force = $force;
    }

    /**
     * The the target path.
     *
     * @return bool
     */
    private function testTarget()
    {
        $targetPath = new Path($this->target, Path::SILENT);
        if ($targetPath->get() === false) {
            return false;
        }
        $this->target = $targetPath->get();

        return true;
    }

    /**
     * Test the link path.
     *
     * @return bool
     *
     * @throws Exception If the link exists and is not a link
     */
    private function testLink()
    {
        $linkPath = new Path($this->link, Path::SILENT);
        if ($linkPath->get() !== false) {
            $this->link = $linkPath->get();

            if (!$this->force) {
                return false;
            }

            if (is_link($this->link)) {
                unlink($this->link);

                return true;
            }

            $msg = sprintf(
                '"%s" existe déjà et n\'est pas un lien symbolique',
                $this->link
            );

            throw new Exception($msg);
        }

        $parent = pathinfo($this->link, PATHINFO_DIRNAME);
        self::createDir($parent);
        $parentPath = new Path($parent, Path::SILENT);

        $this->link = $parentPath->get() . '/' . pathinfo($this->link, PATHINFO_BASENAME);

        return true;
    }

    /**
     * Create the symbolic link.
     *
     * @return void
     *
     * @throws Exception Si la création du lien symbolique échoue
     */
    public function create()
    {
        if (!$this->testTarget()
            || !$this->testLink()
        ) {
            return false;
        }

        $status = @symlink($this->target, $this->link);
        if (!$status) {
            $msg = sprintf(
                'La création du lien symbolique "%s" vers "%s" a échouée',
                $this->link,
                $this->target
            );

            throw new Exception($msg);
        }

        return true;
    }

    /**
     * Créer un dossier en vérifiant au préalable s'il existe déjà.
     *
     * @param string $path Le chemin du dossier
     *
     * @return bool
     *
     * @throws Exception Si le fichier existe déjà mais n'est pas un dossier
     */
    public static function createDir($path)
    {
        if (file_exists($path)
            && is_dir($path)
        ) {
            return true;
        }

        if (file_exists($path)) {
            $msg = 'On ne peut pas créer le dossier "%s" car il existe déjà '
                 . 'mais n\'est pas un dossier';

            throw new Exception(
                sprintf(
                    $msg,
                    $path
                )
            );
        }

        return mkdir($path, 0777, true);
    }
}
