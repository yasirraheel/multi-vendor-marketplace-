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
 namespace App\Http\Controllers\Installer; use App\Http\Controllers\Installer\Helpers\EnvironmentManager; use Illuminate\Http\Request; use Illuminate\Routing\Controller; use Illuminate\Routing\Redirector; use Validator; class EnvironmentController extends Controller { protected $EnvironmentManager; public function __construct(EnvironmentManager $environmentManager) { $this->EnvironmentManager = $environmentManager; } public function environmentMenu() { return view("\151\156\x73\164\141\x6c\154\145\162\x2e\x65\156\x76\151\162\157\156\155\145\x6e\164"); } public function environmentWizard() { } public function environmentClassic() { $envConfig = $this->EnvironmentManager->getEnvContent(); return view("\x69\156\163\164\x61\x6c\x6c\145\162\56\x65\x6e\x76\151\162\157\x6e\x6d\x65\156\164\x2d\x63\154\x61\163\163\x69\143", compact("\x65\x6e\x76\x43\x6f\156\146\151\x67")); } public function saveClassic(Request $input, Redirector $redirect) { $message = $this->EnvironmentManager->saveFileClassic($input); return $redirect->route("\x49\x6e\163\x74\x61\x6c\154\145\x72\56\145\x6e\x76\151\x72\157\156\x6d\x65\156\164\103\x6c\x61\163\163\151\143")->with(["\155\x65\163\x73\141\147\x65" => $message]); } }
