<?php
// Here you can initialize variables that will be available to your tests

TestSetup::init();

class TestSetup {

    /**
     * Initializes the test setup.
     * @return null
     */
    public static function init() {
        /**
         * Delete phantomJS screenshots.
         */
        $phantomJsRecords = glob('tests/_output/record*');

        if (!empty($phantomJsRecords)) {
            self::rrmdir('tests/_output/');
            mkdir('tests/_output/');
        }
    }

    /**
     * Delete folder contents recursively.
     */
    private static function rrmdir($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir."/".$object) == "dir") self::rrmdir($dir."/".$object); else unlink($dir."/".$object);
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

}

