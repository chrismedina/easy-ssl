<?php
/**
 * Created by PhpStorm.
 * User: Godspleb
 * Date: 3/21/2020
 * Time: 12:21 AM
 */

class ESSL_FileSystem {

    public function copy_file($source, $destination, $with_verification = false )
    {
        // .htaccess date("m.d.y");  microtime();

        if (!copy($source, $destination)) {
           // echo "failed to copy $file...\n";
        }

        return true;
    }

}