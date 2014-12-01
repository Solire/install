<?php
namespace Solire\Install;

use Composer\Script\Event;

/**
 * Manage git configuration
 *
 * @author  thansen <thansen@solire.fr>
 * @license CC by-nc http://creativecommons.org/licenses/by-nc/3.0/fr/
 */
class Git
{
    /**
     * Delete current git and recreate new one for the project
     *
     * @param Event $event The composer event
     *
     * @return void
     */
    public static function reinit(Event $event)
    {
        $cmd = 'rm -Rf .git';
        exec($cmd, $output, $return_var);

        $cmd = 'git init';
        exec($cmd, $output, $return_var);

        $cmd = 'git add .';
        exec($cmd, $output, $return_var);

        $cmd = 'git commit -m"Git\'s on (comment generated by composer project Solire Skeleton install script)"';
        exec($cmd, $output, $return_var);
    }
}
