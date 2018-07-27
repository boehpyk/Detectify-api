<?php

require_once __DIR__.'/../config/dev.php';

$dsn = "mysql:host=".$app['db.options']['dbhost'].";dbname=".$app['db.options']['dbname'].";charset=utf8";

$opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
$pdo = new PDO($dsn, $app['db.options']['user'], $app['db.options']['password'], $opt);

$datafile = __DIR__.'/../__data/sample.json';

$source = fopen($datafile, 'r');

if ($source) {
    while (($line = fgets($source)) !== false) {
        $obj = json_decode($line, true);
        $sql = 'INSERT INTO TableIndex (pageTitle) VALUES (:pageTitle)';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue('pageTitle', $obj['pageTitle']);
        $stmt->execute();
        $id = $pdo->lastInsertId();

        $sql = 'INSERT INTO TableContent (id, json) VALUES (:id, :json)';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue('id', $id);
        $stmt->bindValue('json', $line);
        $stmt->execute();

        echo $id."\n";
    }

    fclose($source);
} else {
    die('Error opening file');
}