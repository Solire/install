<?php
namespace Solire\Install;

use Composer\Script\Event;

use Solire\Install\Lib\Ini;

/**
 * Install the databases needed in the project
 *
 * @author  thansen <thansen@solire.fr>
 * @license CC by-nc http://creativecommons.org/licenses/by-nc/3.0/fr/
 */
class Db
{
    /**
     * Install the project database
     *
     * @param Event $event The composer event
     *
     * @return void
     */
    public static function create(Event $event)
    {
        $io = $event->getIO();

        $extras = $event->getComposer()->getPackage()->getExtra();
        $dataBases = $extras['solire']['db'];

        foreach ($dataBases as $dataBase) {
            $config = Ini::parse($dataBase['config']);
            $section = $dataBase['section'];
            $server = new DbServer($config[$section], $io);

            $dbCreated = $server->connect();
            if ($dbCreated) {
                $server->import($dataBase['import']);
            }
        }
    }
}
