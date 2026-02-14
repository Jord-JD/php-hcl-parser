<?php

namespace JordJD\HCLParser;

/**
 * Class Installer.
 */
abstract class Installer
{
    /**
     * json2hcl Version we want to use.
     *
     * @see https://github.com/kvz/json2hcl/releases
     */
    const JSON2HCL_VERSION = '0.0.6';

    /**
     * Returns the correct binary filename according to the Operating System and Architecture.
     */
    public static function getBinaryFilename()
    {
        // Defaults
        $osString = 'linux';
        $architecture = 'amd64';

        // We can not test alternative architectures and operating systems, so exclude from code coverage.
        // @codeCoverageIgnoreStart

        // Switch architecture if needed
        if (2147483647 == PHP_INT_MAX) {
            $architecture = '386';
        }

        // Switch Operating System if needed
        switch (true) {
            case stristr(PHP_OS, 'DAR'):
                $osString = 'darwin';
                break;
            case stristr(PHP_OS, 'WIN'):
                $osString = 'windows';
                break;
        }

        // @codeCoverageIgnoreEnd

        return sprintf('json2hcl_v%s_%s_%s', self::JSON2HCL_VERSION, $osString, $architecture);
    }

    public static function installBinaries()
    {
        $binaryUrls = [
            sprintf('https://github.com/kvz/json2hcl/releases/download/v%s/%s', self::JSON2HCL_VERSION, self::getBinaryFilename()),
        ];

        foreach ($binaryUrls as $binaryUrl) {
            $destination = __DIR__.'/../bin/'.basename($binaryUrl);

            // Skip if file exists
            if (file_exists($destination)) {
                continue;
            }

            // Download
            file_put_contents($destination, file_get_contents($binaryUrl));

            // Make executable
            chmod($destination, 0755);
        }
    }
}
