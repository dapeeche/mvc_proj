<?php
use Core\Config;
use Core\Db;
function config(string $name): string | null
{
    return Config::get($name);
}

function db(): PDO
{
    return Db::connect();
}