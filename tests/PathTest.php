<?php

use FT\Utils\Utils;
use PHPUnit\Framework\TestCase;

final class PathTest extends TestCase {

    /**
    * @test
    */
    public function should_scan_for_files_test() {
        $files= Utils::$Path::scan_for_files(__DIR__, "\.php$");
        $this->assertNotEmpty($files);

        $filenames = array_map(fn ($i) => $i->getFilename(), $files);

        $this->assertContains("PathTest.php", $filenames);
        $this->assertContains("PregTest.php", $filenames);
        $this->assertContains("StringsTest.php", $filenames);
    }

    /**
    * @test
    */
    public function should_scan_for_files_ignoring_test() {
        $files = Utils::$Path::scan_for_files(__DIR__ . '/../', "\.php$", ignore: ['/vendor/?.*']);
        $this->assertNotEmpty($files);

        $filenames = array_map(fn ($i) => $i->getFilename(), $files);

        $this->assertContains("PathTest.php", $filenames);
        $this->assertContains("PregTest.php", $filenames);
        $this->assertContains("StringsTest.php", $filenames);
    }

}