<?php

namespace JordJD\HCLParser;

use JordJD\HCLParser\Exceptions\HCLParseException;

class HCLParser
{
    private $hcl;

    public function __construct($hcl)
    {
        $this->hcl = $hcl;
    }

    private function getBinaryPath()
    {
        $binaryPath = __DIR__.'/../bin/'.Installer::getBinaryFilename();

        if (!file_exists($binaryPath)) {
            Installer::installBinaries();
        }

        return $binaryPath;
    }

    private function getJSONString()
    {
        $pipes = [];
        $process = proc_open(
            [$this->getBinaryPath(), '--reverse'],
            [
                0 => ['pipe', 'r'],
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w'],
            ],
            $pipes
        );

        if (!is_resource($process)) {
            throw new HCLParseException('Unable to start the HCL parser process.');
        }

        fwrite($pipes[0], $this->hcl);
        fclose($pipes[0]);
        $json = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        $error = stream_get_contents($pipes[2]);
        fclose($pipes[2]);
        $exitCode = proc_close($process);

        if ($exitCode !== 0) {
            throw new HCLParseException(
                'The HCL parser failed'.($error ? ': '.trim($error) : '.')
            );
        }

        return $json;
    }

    public function parse()
    {
        $decoded = json_decode($this->getJSONString());

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new HCLParseException('The HCL parser returned invalid JSON: '.json_last_error_msg());
        }

        return $decoded;
    }
}
