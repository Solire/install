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
        $projectName = $event->getIO()->ask($q, $name);
        $projectCode = String::urlSlug($projectName);

        /*
         * Change project's name in config/main.ini
         */
        $path = 'config/main.default.ini';
        $newPath = 'config/main.ini';
        $mainConfig = Lib\Ini::parse($path);
        $mainConfig['project']['name'] = $projectName;
        $mainConfig['project']['code'] = $projectCode;
        Lib\Ini::write($newPath, $mainConfig);

        /*
         * Netbeans config
         */
        self::netBeans($projectName);
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
