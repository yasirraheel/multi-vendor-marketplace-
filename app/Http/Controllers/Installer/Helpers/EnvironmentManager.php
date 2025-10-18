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
 namespace App\Http\Controllers\Installer\Helpers; use Exception; use Illuminate\Http\Request; class EnvironmentManager { private $envPath; private $envExamplePath; public function __construct() { $this->envPath = base_path("\56\145\x6e\x76"); $this->envExamplePath = base_path("\x2e\145\156\166\x2e\x65\x78\x61\155\160\x6c\x65"); } public function getEnvContent() { if (file_exists($this->envPath)) { goto A7ys0; } if (file_exists($this->envExamplePath)) { goto ChAzn; } touch($this->envPath); goto MxNjI; ChAzn: copy($this->envExamplePath, $this->envPath); MxNjI: A7ys0: return file_get_contents($this->envPath); } public function getEnvPath() { return $this->envPath; } public function getEnvExamplePath() { return $this->envExamplePath; } public function saveFileClassic(Request $input) { $message = trans("\x69\x6e\163\164\141\154\x6c\x65\x72\x5f\x6d\145\x73\163\x61\147\145\x73\56\x65\156\x76\x69\162\x6f\156\155\145\156\x74\x2e\163\x75\143\143\x65\163\163"); try { file_put_contents($this->envPath, $input->get("\x65\x6e\x76\x43\157\x6e\x66\x69\x67")); } catch (Exception $e) { $message = trans("\151\x6e\x73\164\x61\x6c\x6c\145\x72\137\155\145\x73\163\x61\x67\x65\163\x2e\145\156\166\x69\x72\157\156\155\145\156\164\x2e\x65\x72\162\x6f\x72\163"); } return $message; } }
