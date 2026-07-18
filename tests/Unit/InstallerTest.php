<?php

use JordJD\HCLParser\Installer;
use PHPUnit\Framework\TestCase;

class InstallerTest extends TestCase
{
    public function testBinariesInstallation()
    {
        foreach (glob(__DIR__.'/../../bin/*') as $file) {
            unlink($file);
        }

        // Check binaries install after deletion
        Installer::installBinaries();
        $this->checkBinariesAreInstalled();

        // Check binaries remain installed if call is made a second time
        Installer::installBinaries();
        $this->checkBinariesAreInstalled();
    }

    private function checkBinariesAreInstalled()
    {
        $files = glob(__DIR__.'/../../bin/*');

        array_walk($files, function (&$value) {
            $value = basename($value);
        });

        $expectedFiles = ['json2hcl_v0.0.6_linux_amd64'];

        foreach ($expectedFiles as $expectedFile) {
            $this->assertContains($expectedFile, $files);
            $this->assertSame(
                Installer::CHECKSUMS[$expectedFile],
                hash_file('sha256', __DIR__.'/../../bin/'.$expectedFile)
            );
        }
    }
}
