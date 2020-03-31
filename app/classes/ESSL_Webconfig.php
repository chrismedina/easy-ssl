<?php
/**
 * Created by PhpStorm.
 * User: Godspleb
 * Date: 3/21/2020
 * Time: 9:44 AM
 */

class ESSL_Webconfig extends ESSL_Config implements ESSL_IServerConfig
{
    protected $config_file = 'web.config';

    function getErrorOutput()
    {
        return $this->copy_error_output;
    }

    function getWriteError()
    {
        return $this->write_error_output;
    }

    function exampleConfig()
    {
        $example = '<?xml version="1.0" encoding="UTF-8"?>
                    <configuration>
                    <system.webServer>
                    <rewrite>
                    <rules>
                    <clear />
                    <rule name="EasySSL.cc Redirect to HTTPS" stopProcessing="true">
                    <match url="(.*)" />
                    <conditions>
                        <add input="{HTTPS}" pattern="off" ignoreCase="true" />
                    </conditions>
                    <action type="Redirect" url="https://{HTTP_HOST}{REQUEST_URI}" redirectType="Permanent" />
                    </rule>
                    <rule name="EasySSL.cc WordPress Main Rule" stopProcessing="true">
                    <match url=".*" />
                    <conditions logicalGrouping="MatchAll">
                    <add input="{REQUEST_FILENAME}" matchType="IsFile" negate="true" />
                    <add input="{REQUEST_FILENAME}" matchType="IsDirectory" negate="true" />
                    </conditions>
                    <action type="Rewrite" url="index.php" />
                    </rule>
                    </rules>
                    </rewrite>
                    </system.webServer>
                    </configuration>';

        return $example;
    }

}