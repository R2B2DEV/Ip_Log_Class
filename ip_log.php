<?php

/** 
 * R2B2- Src - Classes - Ip Log
 * php version 7
 *
 * @category Ninguno
 * @package  Ninguno
 * @author   Ninguno <Ninguno@ninguno.com>
 * @license  Ninguno Ninguno.com
 * @link     Ninguno
 */

namespace R2B2;

/**
 * Html
 * ----------------------------------------------------------------------------------
 * Clase para enviar elementos html y bulma.
 *
 * @category Ninguno
 * @package  Ninguno
 * @author   Ninguno <Ninguno@ninguno.com>
 * @license  Ninguno Ninguno.com
 * @link     Ninguno
 */
class IpLog
{
    private $_location       = [
        "country" => ["id" => null, "name" => null],
        "region"  => ["id" => null, "name" => null],
        "city"    => ["id" => null, "name" => null],
    ];
    private $_ip                 = ["id" => null, "address" => null];
    private $_requestMethod      = ["id" => null, "method"  => null];
    private $_requestUri         = ["id" => null, "uri"     => null];
    private $_siteRefer          = ["id" => null, "site"    => null];
    private $_userAgent          = ["id" => null, "name"    => null];
    private $_userAgentTable     = "ip_log_users_agent";
    private $_countrysTable      = "ip_log_countrys";
    private $_regionsTable       = "ip_log_regions";
    private $_citysTable         = "ip_log_citys";
    private $_ipLogsTable        = "ip_logs";
    private $_ipTable            = "ip_log_ips";
    private $_requestMethodTable = "ip_log_request_methods";
    private $_requestUriTable    = "ip_log_request_uri";
    private $_referredSiteTable  = "ip_log_referred_site";
    private $_savedLogId           = null;
    /**
     * Funcion constructora
     * ------------------------------------------------------------------------------
     * Construye -.-
     * 
     * @return void
     */
    function __construct()
    {
        $this->_ip["address"]            = $_SERVER["REMOTE_ADDR"];
        $this->_requestMethod["method"] = $_SERVER["REQUEST_METHOD"];
        $this->_requestUri["uri"]       = $_SERVER["REQUEST_URI"];
        $this->_siteRefer["site"]
            = $_SERVER["HTTP_REFERER"] ?? "direct_connection";
        $this->_userAgent
            = ["id" => null, "name" => $_SERVER["HTTP_USER_AGENT"]];
        $this->_checkUserAgent();
        $this->_checkIp();
        $this->_checkRequestMethod();
        $this->_checkRequestUri();
        $this->_checkReferredSite();
        $this->_getIpInfo();
        $this->_checkCountry();
        $this->_checkRegion();
        $this->_checkCity();
    }
    /**
     * Verifica User Agent
     * ------------------------------------------------------------------------------
     * Verifica si el User Agent existe en la base de datos, si no existe, lo crea.
     * 
     * @return void
     */
    private function _checkUserAgent()
    {
        global $db;
        $db->where("name", $this->_userAgent["name"]);
        $results = $db->get($this->_userAgentTable);
        if (empty($results)) {
            $insertData
                = ["id" => null, "name" => $this->_userAgent["name"]];
            $id = $db->insert($this->_userAgentTable, $insertData);
        } else {
            $id = $results[0]["id"];
        }
        $this->_userAgent["id"] = $id;
    }
    /**
     * Verifica IP
     * ------------------------------------------------------------------------------
     * Verifica si la IP existe en la base de datos, si no existe, la crea.
     * 
     * @return void
     */
    private function _checkIp()
    {
        global $db;
        $db->where("address", $this->_ip["address"]);
        $results = $db->get($this->_ipTable);
        if (empty($results)) {
            $insertData = ["id" => null, "address" => $this->_ip["address"]];
            $id = $db->insert($this->_ipTable, $insertData);
        } else {
            $id = $results[0]["id"];
        }
        $this->_ip["id"] = $id;
    }
    /**
     * Verifica Metodo de Solicitud
     * ------------------------------------------------------------------------------
     * Verifica si la Metodo de Solicitud existe en la base de datos, si no existe,
     *  la crea.
     * 
     * @return void
     */
    private function _checkRequestMethod()
    {
        global $db;
        $db->where("method", $this->_requestMethod["method"]);
        $results = $db->get($this->_requestMethodTable);
        if (empty($results)) {
            $insertData = [
                "id" => null, "method" => $this->_requestMethod["method"]
            ];
            $id = $db->insert($this->_requestMethodTable, $insertData);
        } else {
            $id = $results[0]["id"];
        }
        $this->_requestMethod["id"] = $id;
    }
    /**
     * Verifica URI solicitada
     * ------------------------------------------------------------------------------
     * Verifica si la URI solicitada existe en la base de datos, si no existe,
     *  la crea.
     * 
     * @return void
     */
    private function _checkRequestUri()
    {
        global $db;
        $db->where("uri", $this->_requestUri["uri"]);
        $results = $db->get($this->_requestUriTable);
        if (empty($results)) {
            $insertData = ["id" => null, "uri" => $this->_requestUri["uri"]];
            $id = $db->insert($this->_requestUriTable, $insertData);
        } else {
            $id = $results[0]["id"];
        }
        $this->_requestUri["id"] = $id;
    }
    /**
     * Verifica Sitio Referido
     * ------------------------------------------------------------------------------
     * Verifica si la Sitio Referido existe en la base de datos, si no existe,
     *  la crea.
     * 
     * @return void
     */
    private function _checkReferredSite()
    {
        global $db;
        $db->where("site", $this->_siteRefer["site"]);
        $results = $db->get($this->_referredSiteTable);
        if (empty($results)) {
            $insertData = ["id" => null, "site" => $this->_siteRefer["site"]];
            $id = $db->insert($this->_referredSiteTable, $insertData);
        } else {
            $id = $results[0]["id"];
        }
        $this->_siteRefer["id"] = $id;
    }
    /**
     * Obtener informacion de la IP
     * ------------------------------------------------------------------------------
     * Obtiene informacion de la direccion ip del cliente conectado a traves de
     * ipinfo.io
     * 
     * @return void
     */
    private function _getIpInfo()
    {
        $details = executeCurl(
            "http://ipinfo.io/" . $this->_ip["address"] . "?token=" . IP_INFO_TOKEN
        );
        if (isset($details->bogon) or is_null($details)) {
            $this->_location["city"]["name"]    = "local";
            $this->_location["region"]["name"]  = "local";
            $this->_location["country"]["name"] = "local";
        } else {
            $this->_location["city"]["name"]    = $details->city;
            $this->_location["region"]["name"]  = $details->region;
            $this->_location["country"]["name"] = $details->country;
        }
    }
    /**
     * Verifica Pais
     * ------------------------------------------------------------------------------
     * Verifica si el pais existe en la base de datos, si no existe, lo crea.
     * 
     * @return void
     */
    private function _checkCountry()
    {
        global $db;
        $db->where("name", $this->_location["country"]["name"]);
        $results = $db->get($this->_countrysTable);
        if (empty($results)) {
            $insertData
                = ["id" => null, "name" => $this->_location["country"]["name"]];
            $id = $db->insert($this->_countrysTable, $insertData);
        } else {
            $id = $results[0]["id"];
        }
        $this->_location["country"]["id"] = $id;
    }
    /**
     * Verifica region
     * ------------------------------------------------------------------------------
     * Verifica si el region existe en la base de datos, si no existe, lo crea.
     * 
     * @return void
     */
    private function _checkRegion()
    {
        global $db;
        $db->where("name", $this->_location["region"]["name"]);
        $db->where("country_id", $this->_location["country"]["id"]);
        $results = $db->get($this->_regionsTable);
        if (empty($results)) {
            $insertData = [
                "id" => null,
                "country_id" => $this->_location["country"]["id"],
                "name" => $this->_location["region"]["name"]
            ];
            $id = $db->insert($this->_regionsTable, $insertData);
        } else {
            $id = $results[0]["id"];
        }
        $this->_location["region"]["id"]  = $id;
    }
    /**
     * Verifica region
     * ------------------------------------------------------------------------------
     * Verifica si el region existe en la base de datos, si no existe, lo crea.
     * 
     * @return void
     */
    private function _checkCity()
    {
        global $db;
        $db->where("name", $this->_location["city"]["name"]);
        $db->where("region_id", $this->_location["region"]["id"]);
        $results = $db->get($this->_citysTable);
        if (empty($results)) {
            $insertData = [
                "id" => null,
                "region_id" => $this->_location["region"]["id"],
                "name" => $this->_location["city"]["name"]
            ];
            $id = $db->insert($this->_citysTable, $insertData);
        } else {
            $id = $results[0]["id"];
        }
        $this->_location["city"]["id"] = $id;
    }
    /**
     * Guardar registro
     * ------------------------------------------------------------------------------
     * Guarda todos los datos del registro en la base de datos.
     * 
     * @return void
     */
    public function saveLog()
    {
        global $db;
        $insertData = [
            "id"                  => null,
            "date"                => $db->now(),
            "ip_id"               => $this->_ip["id"],
            "user_id"             => null,
            "country_id"          => $this->_location["country"]["id"],
            "region_id"           => $this->_location["region"]["id"],
            "city_id"             => $this->_location["city"]["id"],
            "requested_uri_id"    => $this->_requestUri["id"],
            "referred_site_id"    => $this->_siteRefer["id"],
            "requested_method_id" => $this->_requestMethod["id"],
            "user_agent_id"       => $this->_userAgent["id"]
        ];
        if ($this->_requestUri["uri"] != "/favicon.ico") {
            $this->_savedLogId = $db->insert($this->_ipLogsTable, $insertData);
        }
    }
    /**
     * Denegar Acceso
     * ------------------------------------------------------------------------------
     * Verifica si la direccio ip esta en la lista negra y restringe el acceso.
     * 
     * @return void
     */
    public function denyAccess()
    {
        global $db;
        $db->where("address", $this->_ip["address"]);
        $db->where("blacklisted", true);
        $blacklist = $db->get($this->_ipTable, null, "blacklisted");
        if (!empty($blacklist)) {
            exit("access denied\n");
        }
    }

    /**
     * Denegar Acceso
     * ------------------------------------------------------------------------------
     * Verifica si la direccio ip esta en la lista negra y restringe el acceso.
     * 
     * @return void
     */
    public function showIpInfo()
    {
        if (SHOW_IP_INFO) {
            if ($this->_savedLogId == false) {
                echo "<div class='notification is-danger m-0 px-1 py-0 is-size-7'>"
                    . "IP ERROR"
                    . "</div>";
            } else {
                echo "<div class='notification is-warning m-0 px-1 py-0 is-size-7'>"
                    . "<strong>id: </strong>"
                    . $this->_savedLogId
                    . " | <strong>Ip: </strong>"
                    . $this->_ip["address"]
                    . " | <strong>Country: </strong>"
                    . $this->_location["country"]["name"]
                    . " | <strong>Region: </strong>"
                    . $this->_location["region"]["name"]
                    . " | <strong>City: </strong>"
                    . $this->_location["city"]["name"]
                    . " | <strong>Requested uri: </strong>"
                    . $this->_requestUri["uri"]
                    . " | <strong>Referred site: </strong>"
                    . $this->_siteRefer["site"]
                    . " | <strong>Request method: </strong>"
                    . $this->_requestMethod["method"]
                    . " | <strong>User agent: </strong>"
                    . $this->_userAgent["name"]
                    . "</div>";
            }
        }
    }
}
