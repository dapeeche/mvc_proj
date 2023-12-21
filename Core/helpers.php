<?php

function config(string $name): string | null
{
    return \Core\Config::get($name);
}

function db(): PDO
{
    return \Core\Db::connect();
}