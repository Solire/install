<?php

namespace Solire\Install\Lib;

/**
 * Description of Dir.
 *
 * @author  thansen <thansen@solire.fr>
 * @license CC by-nc http://creativecommons.org/licenses/by-nc/3.0/fr/
 */
class Dir
{
    /**
     * Returns the current dir name.
     *
     * @return string
     */
    public static function getDirName()
    {
        return pathinfo(realpath('.'), PATHINFO_FILENAME);
    }
}
