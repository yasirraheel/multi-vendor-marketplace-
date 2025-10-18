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
 namespace App\Http\Controllers\Installer; use App\Http\Controllers\Installer\Helpers\DatabaseManager; use Exception; use Illuminate\Routing\Controller; use Illuminate\Support\Facades\DB; class DatabaseController extends Controller { private $databaseManager; public function __construct(DatabaseManager $databaseManager) { $this->databaseManager = $databaseManager; } public function database() { if ($this->checkDatabaseConnection()) { goto z2jsj; } return redirect()->back()->withErrors(["\144\141\164\141\x62\x61\163\x65\137\x63\157\x6e\156\x65\x63\164\151\157\156" => trans("\x69\156\x73\164\141\x6c\154\x65\x72\x5f\x6d\145\x73\163\x61\x67\145\x73\x2e\145\x6e\166\x69\x72\157\x6e\155\145\156\x74\x2e\x77\151\x7a\141\162\x64\x2e\x66\x6f\x72\x6d\x2e\x64\142\x5f\x63\x6f\156\156\x65\143\x74\x69\x6f\x6e\137\146\x61\151\154\145\144")]); z2jsj: ini_set("\155\141\170\x5f\x65\x78\x65\x63\x75\164\x69\x6f\156\137\x74\151\x6d\x65", 600); $response = $this->databaseManager->migrateAndSeed(); return redirect()->route("\x49\x6e\x73\x74\141\154\154\145\x72\56\x66\151\156\x61\154")->with(["\155\145\163\x73\141\147\145" => $response]); } private function checkDatabaseConnection() { try { DB::connection()->getPdo(); return true; } catch (Exception $e) { return false; } } }
