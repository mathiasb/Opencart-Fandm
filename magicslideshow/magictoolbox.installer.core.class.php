<?php

    if(!function_exists('file_put_contents')) {
        function file_put_contents($filename, $data) {
            $fp = fopen($filename, 'w+');
            if ($fp) {
                fwrite($fp, $data);
                fclose($fp);
            }
        }
    }

    class MagicToolboxCoreInstallerClass {

        var $errors = array();
        var $status = array('stopped', '');
        var $dir = './..';
        var $modDir = './module';
        var $backupSufix = '~backup~created~by~magictoolbox~team';
        var $backups = array();
        var $resDir = '';
        var $installMode = '';

        function MagicToolboxCoreInstallerClass() {
            //$this->errors = array();
            //$this->status = array('stopped', '');
        }

        function setError($messages, $prefix = '') {
            if(!is_array($messages)) {
                $messages = array($messages);
            }
            foreach($messages as $message) {
                $this->errors[] = $prefix . $message;
            }
        }

        function getErrors($html = true) {
            return implode($html ? '<br />' : '\n\r', $this->errors);
        }

        function setStatus($status, $subStatus = '') {
            $this->status = array($status, $subStatus);
        }

        function getStatus($sub = false) {
            return $this->status[$sub?1:0];
        }

        function checkStatus() {
            $status = $this->getStatus();
            if($status == 'done') {
                return true;
            } else {
                return false;
            }
        }

        function setBackups() {
            if(empty($this->backups)) return;
            $this->setError('Installer has modified following OpenCart files:');
            $this->setError(array_keys($this->backups), '&nbsp;&nbsp;&nbsp;-&nbsp;');
            $this->setError('&nbsp;');
            $this->setError('&nbsp;');
            $this->setError('Installer has created backups for all modified files with \'' . $this->backupSufix . '\' suffix in the name:');
            $this->setError($this->backups, '&nbsp;&nbsp;&nbsp;-&nbsp;');
        }

        function run($uninstall = false, $upgrade = false) {
            sleep(2);
            $this->installMode = $uninstall?'uninstall':($upgrade?'upgrade':'install');
            if($this->check()) {
                if($uninstall || $this->backup()) {
                    if($uninstall && $this->uninstall() || $upgrade && $this->_upgrade() || !$uninstall && !$upgrade && $this->install()) {
                        $this->setStatus('done');
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        function check() {
            $this->setStatus('check');
            if($this->checkPlace()) {
                return $this->checkPerm();
            } else {
                return false;
            }
        }

        function checkPlace() {
            $this->setStatus('check', 'place');
            return true;
        }

        function checkPerm() {
            $this->setStatus('check', 'perm');
            return true;
        }

        function backup() {
            $this->setStatus('backup');
            if($this->backupFiles()) {
                return $this->backupDB();
            } else {
                return false;
            }
        }

        function backupFiles() {
            $this->setStatus('backup', 'files');
            return true;
        }

        function restoreStep_backupFiles() {
            return true;
        }

        function backupDB() {
            $this->setStatus('backup', 'DB');
            return true;
        }

        function restoreStep_backupDB() {
            return true;
        }

        function install() {
            $this->setStatus('install');
            if($this->installFiles()) {
                return $this->installDB();
            } else {
                return false;
            }
        }

        function installFiles() {
            $this->setStatus('install', 'files');
            return true;
        }

        function restoreStep_installFiles() {
            return true;
        }

        function installDB() {
            $this->setStatus('install', 'DB');
            return true;
        }

        function restoreStep_installDB() {
            return true;
        }

        function uninstall() {
            $this->setStatus('install', 'DB');
            $this->restore();
            $this->setError('Module was uninstalled!');
            return true;
        }

        function restore() {
            switch($this->getStatus()) {
                case 'install':
                    switch($this->getStatus(true)) {
                        case 'DB':
                            $this->restoreStep_installDB();
                        case 'files':
                            $this->restoreStep_installFiles();
                        default: break;
                    }
                case 'backup':
                    switch($this->getStatus(true)) {
                        case 'DB':
                            $this->restoreStep_backupDB();
                        case 'files':
                            $this->restoreStep_backupFiles();
                        default: break;
                    }
                case 'check':
                case 'stopped':
                default: break;
            }

            $this->setStatus('stopped');
            return true;
        }

        function _upgrade() {
            $this->setStatus('upgrade');
            // here we need to unzip file and upload it
            $zipFile = $_FILES['zipFile']['tmp_name'];

            //NOTE: old code not work with new zip files
            /*
            require_once('zip.class.php');
            $zipFileClass = new zipFile();
            $filesDataOrig = $zipFileClass->read_zip($zipFile);
            $filesData = array();
            foreach($filesDataOrig as $f) {
                $filesData[$f['name']] = $f['data'];
            }
            unset($filesDataOrig);
            */

            if(!extension_loaded('zip')) {
                @dl((strtolower(substr(PHP_OS, 0, 3)) == 'win') ? 'php_zip.dll' : 'zip.so');
            }
            if(extension_loaded('zip')) {
                $zip = zip_open($zipFile);
                $filesData = array();
                if($zip) {
                    while ($zip_entry = zip_read($zip)) {
                        $fileName = basename(zip_entry_name($zip_entry));
                        if (zip_entry_open($zip, $zip_entry, "r")) {
                            $filesData[$fileName] = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
                            zip_entry_close($zip_entry);
                        }
                    }
                    zip_close($zip);
                }
            } else {
                return false;
            }

            $files = array();
            switch('MagicSlideshow') {
                case 'MagicMagnify':
                case 'MagicMagnifyPlus':
                    $files['magicslideshow.swf'] = $filesData['magicslideshow.swf'];
                default:
                    $files['magicslideshow.js'] = $filesData['magicslideshow.js'];
                    break;
            }
            unset($filesData);
            if($this->upgrade($files)) {
                header('Location: congratulations.html');
            } else {
                return false;
            }
        }

        function upgrade($files) {
            return true;
        }

        /*function done() {
            // echo pix.gif image (we need to use ajax.... но аякс это в будущем)
            header("Content-type: image/gif");
            die(base64_decode('R0lGODlhAQABAIAAACqk1AAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw=='));
        }*/

        function checkFilesPerm($files, $perm = 'write') {
            if(!is_array($files)) {
                $files = array($files);
            }
            //$perm = intval($perm);
            $wrang = array();
            foreach($files as $file) {
                //if(intval(substr(decoct(fileperms($this->dir . $file)), -3)) < $perm) {
                    //$wrang[] = $file;
                //}
                if($perm == 'write' && !is_writeable($this->dir . $file) || $perm == 'read' && !is_readable($this->dir . $file)) {
                    $wrang[] = $file;
                }
            }
            return array(empty($wrang)?true:false, $wrang);
        }

        function removeFiles($files) {
            if(!is_array($files)) {
                $files = array($files);
            }
            foreach($files as $file) {
                $fileName = $this->dir . $file;
                @unlink($fileName);
            }
            return true;
        }

        function createBackups($files, $overwrite = false) {
            if(!is_array($files)) {
                $files = array($files);
            }
            $wrang = array();
            foreach($files as $file) {
                $file = $this->dir . $file;
                $info = pathinfo($file);
                if(intval(phpversion()) < 5 || !isset($info["filename"])) {
                    //$info["filename"] = basename($info["basename"], ".".$info["extension"]);
                    $info["filename"] = preg_replace("/\." . preg_quote($info["extension"]) . "$/is", "", $info["basename"]);
                }
                $backupFileName = $info['dirname'] . '/' . $info['filename'] . $this->backupSufix . '.' . $info['extension'];
                if(!file_exists($backupFileName) || $overwrite) {
                    if(!copy($file, $backupFileName)) {
                        $wrang[] = $file;
                    } else {
                        $this->backups[$file] = $backupFileName;
                    }
                } else {
                    $this->backups[$file] = $backupFileName;
                }
            }
            return array(empty($wrang)?true:false, $wrang);
        }

        function removeBackups($files) {
            if(!is_array($files)) {
                $files = array($files);
            }
            foreach($files as $file) {
                $file = $this->dir . $file;
                $info = pathinfo($file);
                if(intval(phpversion()) < 5 || !isset($info["filename"])) {
                    //$info["filename"] = basename($info["basename"], ".".$info["extension"]);
                    $info["filename"] = preg_replace("/\." . preg_quote($info["extension"]) . "$/is", "", $info["basename"]);
                }
                $backupFileName = $info['dirname'] . '/' . $info['filename'] . $this->backupSufix . '.' . $info['extension'];
                @unlink($backupFileName);
            }
            return true;
        }

        function restoreFromBackups($files) {
            if(!is_array($files)) {
                $files = array($files);
            }
            foreach($files as $file) {
                $file = $this->dir . $file;
                $info = pathinfo($file);
                if(intval(phpversion()) < 5 || !isset($info["filename"])) {
                    //$info["filename"] = basename($info["basename"], ".".$info["extension"]);
                    $info["filename"] = preg_replace("/\." . preg_quote($info["extension"]) . "$/is", "", $info["basename"]);
                }
                $backupFileName = $info['dirname'] . '/' . $info['filename'] . $this->backupSufix . '.' . $info['extension'];
                if(file_exists($backupFileName)) {
                    @unlink($file);
                    @copy($backupFileName, $file);
                }
            }
            return true;
        }

        function copyDir($src, $dest, $perm = 0755) {
            if(!is_dir($dest)) {
                mkdir($dest);
            }
            if($dir = @opendir($src)) {
                while (($file = readdir($dir))!==false) {
                    if($file == '.' || $file == '..') {
                        continue;
                    }
                    if(is_dir($src . '/' . $file)) {
                        $this->copyDir($src . '/' . $file, $dest . '/' . $file);
                    } else {
                        if($file == 'magicslideshow.settings.dat') {
                            $file = 'magicslideshow.settings.ini';
                            copy($src . '/magicslideshow.settings.dat', $dest . '/' . $file);
                        } elseif(($file == 'magicslideshow.css' || $file == 'magicscroll.css') && $this->resDir != '') {
                            //fix url's in css files
                            copy($src . '/' . $file, $dest . '/' . $file);
                            $fileContents = file_get_contents($dest . '/' . $file);
                            $pattern = '/url\(\s*(?:\'|")?(?!'.preg_quote($this->resDir, '/').')\/?([^\)\s]+?)(?:\'|")?\s*\)/is';
                            $replace = 'url(' . $this->resDir . '/$1)';
                            $fixedFileContents = preg_replace($pattern, $replace, $fileContents);
                            if($fixedFileContents != $fileContents) {
                                file_put_contents($dest . '/' . $file, $fixedFileContents);
                            }
                        } else {
                            copy($src . '/' . $file, $dest . '/' . $file);
                        }
                        @chmod($dest . '/' . $file, $perm);
                        if(preg_match('/\.(settings\.ini)|(js)|(css)|(swf)$/is', $file)) {
                            @chmod($dest . '/' . $file, 0777);
                        }
                    }
                }
                closedir($dir);
            }
        }

        function copyFileRecursive($src, $dest, $perm = 0755) {
            if(file_exists($dest)) {
                return true;
            }
            if(file_exists($src)) {
                $newDir = preg_replace('/^(.*?)\/[^\/]+\/?$/is', '$1', $dest);
                if(!is_dir($newDir)) {
                    if(!$this->createDirRecursive($newDir, $perm)) {
                        return false;
                    }
                }
                @copy($src, $dest);
                @chmod($dest, $perm);
                if(file_exists($dest)) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        function createDirRecursive($dir, $perm = 0755) {
            if(!is_dir($dir)) {
                $this->createDirRecursive(preg_replace('/^(.*?)\/[^\/]+\/?$/is', '$1', $dir), $perm);
                @mkdir($dir);
                @chmod($dir, $perm);
            }
            if(!is_dir($dir)) {
                return false;
            }
            return true;
        }

        function removeDir($src) {
            if($dir = @opendir($src)) {
                while (($file = readdir($dir))!==false) {
                    if($file == '.' || $file == '..') {
                        continue;
                    }
                    if(is_dir($src . '/' . $file)) {
                        $this->removeDir($src . '/' . $file);
                    } else {
                        unlink($src . '/' . $file);
                    }
                }
                closedir($dir);
            }
            rmdir($src);
        }
    }
