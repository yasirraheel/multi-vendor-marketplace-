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
 namespace App\Http\Controllers\Installer; use App\Http\Controllers\Installer\Helpers\DatabaseManager; use App\Http\Controllers\Installer\Helpers\InstalledFileManager; use Illuminate\Routing\Controller; class UpdateController extends Controller { use \App\Http\Controllers\Installer\Helpers\MigrationsHelper; public function welcome() { return view("\x69\x6e\163\x74\x61\154\154\x65\x72\x2e\x75\160\x64\x61\x74\145\56\167\145\x6c\143\157\x6d\145"); } public function overview() { $migrations = $this->getMigrations(); $dbMigrations = $this->getExecutedMigrations(); return view("\x69\156\163\x74\141\x6c\154\145\162\x2e\165\160\x64\x61\164\145\x2e\x6f\166\x65\x72\166\151\145\x77", ["\156\165\x6d\142\145\x72\x4f\x66\x55\160\144\x61\x74\145\163\120\145\x6e\x64\x69\x6e\x67" => count($migrations) - count($dbMigrations)]); } public function database() { $databaseManager = new DatabaseManager(); $response = $databaseManager->migrateAndSeed(); return redirect()->route("\x4c\141\x72\x61\166\x65\154\125\160\x64\x61\164\145\162\x3a\x3a\x66\151\156\x61\x6c")->with(["\155\x65\163\x73\x61\147\x65" => $response]); } public function finish(InstalledFileManager $fileManager) { $fileManager->update(); return view("\x69\x6e\163\x74\x61\154\154\145\162\56\165\160\144\141\x74\145\56\x66\x69\x6e\x69\x73\150\x65\144"); } }
