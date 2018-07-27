<?php

//namespace console;

//use JsonStreamingParser\Parser;
//use JsonStreamingParser\ParsingError;
//use JsonStreamingParser\ObjectListener;

require_once __DIR__.'/../Util/JSONStreamingParser/Parser.php';
require_once __DIR__.'/../Util/JSONStreamingParser/ObjectListener.php';

class Import
{
    private $testfile;

    function __construct()
    {
        $this->testfile = __DIR__.'/../../_data/sample_small.json'; //https://gist.github.com/soyuka/a1d83ff9ff1a6c5cc269;
    }

    function import()
    {
        $listener = new ObjectListener(function ($obj) {
            var_dump($obj);
        });

        $stream = fopen($this->testfile, 'r');
        try {
            $parser = new Parser($stream, $listener);
            $parser->parse();
        } catch (Exception $e) {
            fclose($stream);
            throw $e;
        }
    }
}

$import = new Import();
$import->import();