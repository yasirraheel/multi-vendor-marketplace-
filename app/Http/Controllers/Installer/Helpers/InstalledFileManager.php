<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  2.0.14  |
    |              on 2025-05-28 15:20:04              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
/*
* Copyright (C) Incevio Systems, Inc - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
* Written by Munna Khan <help.zcart@gmail.com>, September 2018
*/
 namespace App\Http\Controllers\Installer\Helpers; class InstalledFileManager { public function create() { $installedLogFile = storage_path("\x69\156\x73\x74\x61\x6c\x6c\145\144"); $dateStamp = date("\131\57\x6d\x2f\144\x20\x68\x3a\151\72\x73\141"); if (!file_exists($installedLogFile)) { goto VGAr1; } $message = trans("\x69\156\x73\164\x61\x6c\x6c\x65\162\x5f\155\x65\x73\x73\141\x67\145\163\x2e\x75\x70\144\141\x74\145\x72\x2e\154\157\147\x2e\x73\165\143\143\x65\x73\x73\x5f\x6d\145\x73\163\141\147\x65") . $dateStamp; file_put_contents($installedLogFile, $message . PHP_EOL, FILE_APPEND | LOCK_EX); goto EZhhY; VGAr1: $message = trans("\x69\x6e\x73\164\141\x6c\154\x65\162\x5f\x6d\x65\163\x73\x61\x67\145\x73\x2e\x69\x6e\x73\x74\141\154\x6c\x65\144\x2e\x73\x75\143\143\145\163\163\137\154\x6f\x67\137\155\145\163\x73\141\147\145") . $dateStamp . "\12"; file_put_contents($installedLogFile, $message); EZhhY: return $message; } public function update() { return $this->create(); } }
