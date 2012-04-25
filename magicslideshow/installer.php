<?php
    /**
        MagicToolbox installer
    */

    ini_set('display_errors', true);
    error_reporting(E_ALL & ~E_NOTICE);

    if(isset($_GET['mode']) && trim($_GET['mode']) == 'checkZip') {
        if(!extension_loaded('zip')) {
            @dl((strtolower(substr(PHP_OS, 0, 3)) == 'win') ? 'php_zip.dll' : 'zip.so');
        }
        if(extension_loaded('zip')) {
            echo '[loaded]';
        } else {
            echo '[not loaded]';
        }
        return;
    }

    require_once(dirname(__FILE__) . '/magictoolbox.installer.core.class.php');
    require_once(dirname(__FILE__) . '/magictoolbox.installer.opencart.class.php');

    $modInstaller = new MagicToolboxopencartModuleInstallerClass();
    $uninstall = false;
    $upgrade = false;
    if(isset($_GET['mode']) && trim($_GET['mode']) == 'uninstall') {
        $uninstall = true;
    }
    if(isset($_GET['mode']) && trim($_GET['mode']) == 'upgrade') {
        $upgrade = true;
    }
    
    if(!$modInstaller->run($uninstall, $upgrade)) {
        echo '[error]';
        echo $modInstaller->getErrors();
        $modInstaller->restore();
    } else {
        echo '[done]';
        $modInstaller->setBackups();
        echo $modInstaller->getErrors();
    }
