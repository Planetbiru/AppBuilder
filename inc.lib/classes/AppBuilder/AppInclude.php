<?php

namespace AppBuilder;

use MagicObject\MagicObject;
use MagicObject\SecretObject;
use MagicObject\Util\PicoGenericObject;

class AppInclude
{
    /**
     * Main header
     *
     * @param string $dir
     * @param MagicObject|SecretObject $config
     * @return void
     */
    public static function mainAppHeader($dir, $config)
    {
        if($config != null)
        {
            return $dir."/".$config->getInludeBaseDirecory().$config->getInludeHeaderFile();
        }
        else
        {
            return $dir."/header.php";
        }
    }
    
    /**
     * Main footer
     *
     * @param string $dir
     * @param MagicObject|SecretObject $config
     * @return void
     */
    public static function mainAppFooter($dir, $config)
    {
        if($config != null)
        {
            return $dir."/".$config->getInludeBaseDirecory().$config->getInludeFooterFile();
        }
        else
        {
            return $dir."/Footer.php";
        }
    }
}