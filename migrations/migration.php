<?php

define('BASE_DIR', dirname(__DIR__));

require_once BASE_DIR . '/config/constants.php';
require_once BASE_DIR . '/vendor/autoload.php';

$dotenv = \Dotenv\Dotenv::createUnsafeImmutable(BASE_DIR);
$dotenv->load();

class Migration
{
    const SCRIPTS_DIR = __DIR__ . '/scripts/';
    const MIGRATIONS_FILE = '0_migrations';

    protected PDO $db;
    public function __construct()
    {
        $this->db = db();
        try {
            $this->db->beginTransaction();

            $this->createMigrationTable();
            $this->runMigrations();

            if ($this->db->inTransaction()) {
                $this->db->commit();
            }

        } catch (PDOException $exception) {
            var_dump($exception->getMessage(), $exception->getTrace());
            if ($this->db->inTransaction()) {
            $this->db->rollBack();
        }
        }
    }

    protected function runMigrations(): void
    {
        var_dump('-- fetch migrations --');

        $migrations = scandir(static::SCRIPTS_DIR);
        $migrations = array_values(array_diff(
            $migrations,
            ['.', '..', static::MIGRATIONS_FILE . '.sql']
        ));

        foreach($migrations as $script) {
            $table = preg_replace('/[\d]+_/i', '', $script);
        }
        var_dump('--migrations done --');
    }

    protected function createMigrationTable(): void
    {
        var_dump('--Prepare migration table query --');
        $sql = file_get_contents(static::SCRIPTS_DIR . static::MIGRATIONS_FILE . '.sql');
        $query = $this->db->prepare($sql);

        $result = match ($query->execute()) {
            true => 'migration table created',
            false => 'failed'
        };

        var_dump($result, '-- finish --');
    }

} new Migration;