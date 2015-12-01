<?php
namespace Solire\Install;

use Composer\Script\Event;
use Solire\Lib\Format\String;
use Solire\Install\Lib\Ini;

/**
 * Ask for project's name and update différent config with it
 *
 * @author  thansen <thansen@solire.fr>
 * @license CC by-nc http://creativecommons.org/licenses/by-nc/3.0/fr/
 */
class Project
{
    const NETBEANS = 'nbproject/project.xml';

    /**
     * Ask for project's name and update différent config with it
     *
     * @param Event $event The composer event
     *
     * @return void
     */
    public static function name(Event $event)
    {
        $projectCode = Lib\Dir::getDirName();

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

        /*
         * Netbeans config
         */
        self::netBeans($projectName);

        $extra['solire']['parameters'] = $parameters;
        $event->getComposer()->getPackage()->setExtra($extra);
    }

    /**
     * Change the project's name in Netbeans Configuration file
     *
     * @param string $name
     *
     * @return void
     */
    protected static function netBeans($name)
    {
        $dom = new \DomDocument();
        $dom->preserveWhiteSpace = false;
        $dom->load(self::NETBEANS);
        $configuration = $dom->getElementsByTagName('name');
        $configuration->item(0)->nodeValue = $name;
        $dom->save(self::NETBEANS);
    }
}
