<?php

namespace Solire\Install;

use Composer\Script\Event;
use Solire\Install\Lib\Dir;
use Solire\Lib\Format\String;

/**
 * Ask for project's name and update différent config with it.
 *
 * @author  thansen <thansen@solire.fr>
 * @license CC by-nc http://creativecommons.org/licenses/by-nc/3.0/fr/
 */
class Project
{
    const NETBEANS = 'nbproject/project.xml';

    /**
     * Ask for project's name and update différent config with it.
     *
     * @param Event $event The composer event
     *
     * @return void
     */
    public static function name(Event $event)
    {
        $projectCode = Dir::getDirName();

        $q = sprintf(
            '<question>%s</question> (<comment>%s</comment>): ',
            'Project\'s name',
            $projectCode
        );
        $projectName = $event->getIO()->ask($q, $projectCode);
        $projectCode = String::urlSlug($projectName, '_');

        /*
         * Change project's name in config/main.ini
         */
        $extra = $event->getComposer()->getPackage()->getExtra();
        $parameters = $extra['solire']['parameters'];
        $parameters['config/main.yml']['project']['name'] = $projectName;
        $parameters['config/main.yml']['project']['code'] = $projectCode;

        $parameters['config/local.yml']['database']['dbname'] = $projectCode;

        $parameters['config/local.yml']['base']['url'] = 'http://localhost/' . $projectCode . '/';
        $parameters['config/local.yml']['base']['root'] = $projectCode . '/';

        $extra['solire']['parameters'] = $parameters;
        $event->getComposer()->getPackage()->setExtra($extra);
    }
}
