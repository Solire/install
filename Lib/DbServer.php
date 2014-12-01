<?php
namespace Solire\Install\Lib;

use Doctrine\DBAL\DriverManager;
use Composer\IO\IOInterface;

/**
 * Connects to a sql server, creates databases, import sql file
 *
 * @author  thansen <thansen@solire.fr>
 * @license CC by-nc http://creativecommons.org/licenses/by-nc/3.0/fr/
 */

class DbServer
{
    /**
     * Connection to the database
     *
     * @var \Doctrine\DBAL\Connection
     */
    private $connection;

    /**
     * Connection parameters
     *
     * @var array
     */
    private $config;

    /**
     * Input / Output interface
     *
     * @var IOInterface
     */
    private $io;

    /**
     * Constructor
     *
     * @param array       $config The doctrine connection parameters
     * @param IOInterface $io     The input output interface
     */
    public function __construct($config, IOInterface $io)
    {
        $this->config = $config;

        unset($config['dbname']);
        $config['driver'] = 'pdo_mysql';
        $config['driverOptions'] = array(
            \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        );
        $this->connection = DriverManager::getConnection($config);

        $this->io = $io;
    }

    /**
     * Create the database
     *
     * @return boolean
     */
    public function connect()
    {
        $dbName = $this->config['dbname'];
        try {
            $this->connection->getSchemaManager()->createDatabase($dbName);
            $m = sprintf(
                '<info>The database "%s" has been created</info>',
                $this->config['dbname']
            );
            $this->io->write($m);
            return true;
        } catch (\Exception $e) {
            $q = sprintf(
                '<error>The database "%s" already exists, drop current databases ? [y/N]</error>',
                $dbName
            );
            $ans = $this->io->askConfirmation($q);

            if ($ans) {
                $this->connection->getSchemaManager()->dropAndCreateDatabase($dbName);
                $m = sprintf(
                    '<info>The database "%s" has been recreated</info>',
                    $this->config['dbname']
                );
                $this->io->write($m);
                return true;
            }
        }

        return false;
    }

    /**
     * Import a sql file into the database
     *
     * @param type $sqlDumpPath The path to an .sql file
     *
     * @return void
     */
    public function import($sqlDumpPath)
    {
        $cmd = 'mysql -h"' . $this->config['host'] . '" '
             . '-u"' . $this->config['user'] . '" '
             . '-p"' . $this->config['password'] . '" '
             . '' . $this->config['dbname'] . ' '
             . '< "' . $sqlDumpPath . '"';
        exec($cmd);

        $m = sprintf(
            '<info>The database "%s" has been filled</info>',
            $this->config['dbname']
        );
        $this->io->write($m);
    }
}
