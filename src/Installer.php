<?php

namespace JordJD\HCLParser;

use RuntimeException;

abstract class Installer
{
    const JSON2HCL_VERSION = '0.0.6';

    const CHECKSUMS = [
        'json2hcl_v0.0.6_darwin_386' => '4294338d4f16a3f66013364d75ca49e9641a57722c9d93f6fd8d59ae71d1b232',
        'json2hcl_v0.0.6_darwin_amd64' => '547dfd077647a2fdd2258cb72f752c1422ca299f9c0b27501bcc133fac62451d',
        'json2hcl_v0.0.6_linux_386' => '0c988eee018e239a2360b7067508d7b196fd5e4a946ea7ac8b5e19c8e99d2f30',
        'json2hcl_v0.0.6_linux_amd64' => 'd124ed13f3538c465fcab19e6015d311d3cd56f7dc2db7609b6e72fec666482d',
        'json2hcl_v0.0.6_linux_arm' => 'c1ae560925f67942b17fe42d339b04b2cb1adc61c00dada6db8aea7e214bbe8f',
        'json2hcl_v0.0.6_windows_386.exe' => '2e157a10e4bd6b31f9f3664302facef10140a57a54a4bc776fad7d23e49a5691',
        'json2hcl_v0.0.6_windows_amd64.exe' => '33657d19f974c3e98b7df32eb77d01858498eaa81c12314dbaaba94650cc77ae',
    ];

    public static function getBinaryFilename()
    {
        $operatingSystems = [
            'Darwin' => 'darwin',
            'Linux' => 'linux',
            'Windows' => 'windows',
        ];
        $architectures = [
            'amd64' => 'amd64',
            'x86_64' => 'amd64',
            'i386' => '386',
            'i686' => '386',
            'x86' => '386',
            'arm' => 'arm',
            'armv7' => 'arm',
            'armv7l' => 'arm',
        ];
        $operatingSystem = isset($operatingSystems[PHP_OS_FAMILY]) ? $operatingSystems[PHP_OS_FAMILY] : null;
        $machine = strtolower(php_uname('m'));
        $architecture = isset($architectures[$machine]) ? $architectures[$machine] : null;

        if ($operatingSystem === null || $architecture === null || ($operatingSystem !== 'linux' && $architecture === 'arm')) {
            throw new RuntimeException('No json2hcl binary is available for '.PHP_OS_FAMILY.' '.$machine.'.');
        }

        return sprintf(
            'json2hcl_v%s_%s_%s%s',
            self::JSON2HCL_VERSION,
            $operatingSystem,
            $architecture,
            $operatingSystem === 'windows' ? '.exe' : ''
        );
    }

    public static function installBinaries()
    {
        $filename = self::getBinaryFilename();
        $expectedChecksum = self::CHECKSUMS[$filename];
        $destination = __DIR__.'/../bin/'.$filename;

        if (is_file($destination) && hash_equals($expectedChecksum, hash_file('sha256', $destination))) {
            return;
        }

        $url = sprintf(
            'https://github.com/kvz/json2hcl/releases/download/v%s/%s',
            self::JSON2HCL_VERSION,
            $filename
        );
        $context = stream_context_create([
            'http' => ['timeout' => 30],
        ]);
        $binary = @file_get_contents($url, false, $context);

        if ($binary === false) {
            throw new RuntimeException('Unable to download json2hcl from its official GitHub release.');
        }

        if (!hash_equals($expectedChecksum, hash('sha256', $binary))) {
            throw new RuntimeException('Downloaded json2hcl binary failed checksum verification.');
        }

        $temporaryPath = tempnam(dirname($destination), 'json2hcl-');

        if ($temporaryPath === false || file_put_contents($temporaryPath, $binary, LOCK_EX) === false) {
            throw new RuntimeException('Unable to write the json2hcl binary.');
        }

        if (PHP_OS_FAMILY !== 'Windows' && !chmod($temporaryPath, 0755)) {
            @unlink($temporaryPath);
            throw new RuntimeException('Unable to make the json2hcl binary executable.');
        }

        if (is_file($destination)) {
            @unlink($destination);
        }

        if (!rename($temporaryPath, $destination)) {
            @unlink($temporaryPath);
            throw new RuntimeException('Unable to install the json2hcl binary.');
        }
    }
}
