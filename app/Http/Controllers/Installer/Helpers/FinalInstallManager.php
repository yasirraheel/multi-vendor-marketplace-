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
 namespace App\Http\Controllers\Installer\Helpers; use Exception; use Illuminate\Support\Facades\Artisan; use Symfony\Component\Console\Output\BufferedOutput; class FinalInstallManager { public function runFinal() { $outputLog = new BufferedOutput(); $this->generateKey($outputLog); $this->publishVendorAssets($outputLog); return $outputLog->fetch(); } private static function generateKey($outputLog) { try { if (!config("\x69\x6e\163\164\141\154\154\145\162\56\x66\151\x6e\x61\x6c\56\153\x65\x79")) { goto LDErl; } Artisan::call("\153\145\x79\72\147\145\x6e\145\x72\x61\x74\145", ["\x2d\x2d\x66\x6f\x72\143\145" => true], $outputLog); LDErl: } catch (Exception $e) { return static::response($e->getMessage(), $outputLog); } return $outputLog; } private static function publishVendorAssets($outputLog) { try { if (!config("\151\156\163\x74\141\x6c\x6c\x65\x72\56\x66\x69\156\x61\154\x2e\160\165\x62\154\x69\163\x68")) { goto dRoyQ; } Artisan::call("\x76\145\x6e\x64\157\162\x3a\x70\x75\x62\x6c\151\x73\x68", ["\55\55\x61\x6c\154" => true], $outputLog); dRoyQ: } catch (Exception $e) { return static::response($e->getMessage(), $outputLog); } return $outputLog; } private static function response($message, $outputLog) { return ["\163\x74\141\x74\x75\163" => "\x65\162\162\x6f\x72", "\x6d\x65\x73\x73\x61\x67\x65" => $message, "\x64\x62\117\x75\164\160\x75\164\x4c\x6f\x67" => $outputLog->fetch()]; } }
