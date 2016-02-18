<?php

namespace Solire\Install;

use Composer\Script\Event;
use Solire\Install\Lib\DbServer;

/**
 * Install the databases needed in the project.
 *
 * @author  thansen <thansen@solire.fr>
 * @license CC by-nc http://creativecommons.org/licenses/by-nc/3.0/fr/
 */
class Db
{
    /**
     * Install the project database.
     *
     * @param Event $event The composer event
     *
     * @return void
     */
    public static function create(Event $event)
    {
        $io = $event->getIO();

        $extras = $event->getComposer()->getPackage()->getExtra();
        $parameters = $extras['solire']['parameters'];
        $dataBases = $extras['solire']['db'];

        foreach ($dataBases as $dataBase) {
            $dbParam = $parameters[$dataBase['config']][$dataBase['section']];

            $server = new DbServer($dbParam, $io);

            $dbCreated = $server->connect();
            if ($dbCreated) {
                $server->import($dataBase['import']);
            }
        }
    }
}
