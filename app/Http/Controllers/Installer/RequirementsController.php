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
 namespace App\Http\Controllers\Installer; use App\Http\Controllers\Installer\Helpers\RequirementsChecker; use Illuminate\Routing\Controller; class RequirementsController extends Controller { protected $requirements; public function __construct(RequirementsChecker $checker) { $this->requirements = $checker; } public function requirements() { $phpSupportInfo = $this->requirements->checkPHPversion(config("\151\156\x73\164\x61\x6c\154\x65\162\56\143\157\x72\x65\56\x6d\x69\156\x50\x68\x70\x56\x65\162\x73\151\157\x6e"), config("\x69\156\x73\x74\x61\154\154\x65\162\56\143\x6f\162\x65\56\155\141\x78\120\150\160\126\x65\x72\x73\151\157\x6e")); $requirements = $this->requirements->check(config("\151\x6e\163\164\141\x6c\x6c\145\x72\x2e\x72\x65\161\x75\x69\x72\145\155\145\156\164\x73")); return view("\x69\156\x73\164\141\154\154\145\x72\x2e\x72\x65\x71\x75\151\x72\x65\x6d\x65\x6e\x74\163", compact("\162\145\161\165\151\x72\x65\155\x65\x6e\164\x73", "\x70\150\160\123\165\x70\x70\x6f\x72\164\111\156\146\x6f")); } }
