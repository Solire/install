<?php
/**
 * Install the databases needed in the project
 *
 * @author  thansen <thansen@solire.fr>
 * @license CC by-nc http://creativecommons.org/licenses/by-nc/3.0/fr/
 */

namespace Solire\Install;

use Composer\Script\Event;

/**
 * Install the databases needed in the project
 *
 * @author  thansen <thansen@solire.fr>
 * @license CC by-nc http://creativecommons.org/licenses/by-nc/3.0/fr/
 */
class Db
{
    public static function create(Event $event)
    {
        $io = $event->getIO();

        $extras = $event->getComposer()->getPackage()->getExtra();
        $dataBases = $extras['solire']['db'];

        foreach ($dataBases as $dataBase) {
            $config = parse_ini_file($dataBase['config'], true);
            $section = $dataBase['section'];
            $server = new DbServer($config[$section], $io);

            $dbCreated = $server->create();
            if ($dbCreated) {
                $server->import($dataBase['import']);
            }
        }
    }
}
