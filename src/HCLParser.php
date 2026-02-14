<?php

namespace JordJD\HCLParser;

/**
 * Class HCLParser.
 */
class HCLParser
{
    /**
     * @var
     */
    private $hcl;

    /**
     * HCLParser constructor.
     *
     * @param $hcl
     */
    public function __construct($hcl)
    {
        $this->hcl = $hcl;
    }

    /**
     * @return string
     */
    private function getBinaryPath()
    {
        $binaryPath = __DIR__.'/../bin/'.Installer::getBinaryFilename();

        if (!file_exists($binaryPath)) {
            Installer::installBinaries();
        }

        return $binaryPath;
    }

    /**
     * @return string
     */
    private function getJSONString()
    {
        $command = $this->getBinaryPath().' --reverse <<\'EOF\''.PHP_EOL.$this->hcl.PHP_EOL.'EOF';

        exec($command, $lines);

        return implode(PHP_EOL, $lines);
    }

    /**
     * @return mixed
     */
    public function parse()
    {
        return json_decode($this->getJSONString());
    }
}
