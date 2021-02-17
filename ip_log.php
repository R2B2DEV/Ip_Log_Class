<?php

/** 
 * R2B2- Src - Classes - Ip Log
 * php version 7
 *
 * @category Class
 * @package  Ip_Log_Class
 * @author   R2D2DEV <r2d2dev@gmail.com>
 * @license  MIT License
 * @link     https://github.com/R2B2DEV
 */

namespace R2B2;

/**
 * IP Log
 * ----------------------------------------------------------------------------------
 * Clase para obtener la direccion ip del visitante, asi como todos los datos
 * relacionados a este, como el pais, el estado, la ciudad, el metodo de soliicitud,
 * la url solicitudada, el sitio desde donde es referido y el user agent.
 *
 * @category Class
 * @package  Ip_Log_Class
 * @author   R2D2DEV <r2d2dev@gmail.com>
 * @license  MIT License
 * @link     https://github.com/R2B2DEV
 */
class IpLog
{
    // Tables
    private $_userAgentTable     = "ip_log_users_agent";
    private $_countrysTable      = "ip_log_countrys";
    private $_regionsTable       = "ip_log_regions";
    private $_citysTable         = "ip_log_citys";
    private $_ipLogsTable        = "ip_logs";
    private $_ipLogsView         = "view_ip_logs";
    private $_ipsTable           = "ip_log_ips";
    private $_requestMethodTable = "ip_log_request_methods";
    private $_requestUriTable    = "ip_log_request_uri";
    private $_referredSiteTable  = "ip_log_referred_site";
    // Ip Data
    private $_ip                 = ["id" => null, "address" => null];
    private $_requestMethod      = ["id" => null, "method"  => null];
    private $_requestUri         = ["id" => null, "uri"     => null];
    private $_siteRefer          = ["id" => null, "site"    => null];
    private $_userAgent          = ["id" => null, "name"    => null];
    private $_country            = ["id" => null, "name"    => null];
    private $_region             = ["id" => null, "name"    => null];
    private $_city               = ["id" => null, "name"    => null];
    // Others
    private $_lastIdSaved        = null;
    /**
     * Funcion constructora
     * ------------------------------------------------------------------------------
     * Construye -.-
     * 
     * @return void
     */
    function __construct()
    {
        $this->_ip["address"]           = $_SERVER["REMOTE_ADDR"];
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
     * Ejecuta Curl
     * ------------------------------------------------------------------------------
     * Ejecuta Curl con parametros GET
     * 
     * @param string $url url para curl
     *
     * @return object
     */
    private function _executeCurl($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10000);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30000);
        $return = curl_exec($curl);
        $return = json_decode($return);
        curl_close($curl);
        return $return;
    }
    /**
     * Verifica User Agent
     * ------------------------------------------------------------------------------
     * Verifica si el User Agent existe en la base de datos, si no existe, lo guarda.
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
     * Verifica si la IP existe en la base de datos, si no existe, la guarda.
     * 
     * @return void
     */
    private function _checkIp()
    {
        global $db;
        $db->where("address", $this->_ip["address"]);
        $results = $db->get($this->_ipsTable);
        if (empty($results)) {
            $insertData = ["id" => null, "address" => $this->_ip["address"]];
            $id = $db->insert($this->_ipsTable, $insertData);
        } else {
            $id = $results[0]["id"];
        }
        $this->_ip["id"] = $id;
    }
    /**
     * Verifica Metodo de Solicitud
     * ------------------------------------------------------------------------------
     * Verifica si la Metodo de Solicitud existe en la base de datos, si no existe,
     *  la guarda.
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
     *  la guarda.
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
     * Verifica si el Sitio Referido existe en la base de datos, si no existe,
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
     * Obtiene informacion de la direccion ip del cliente a traves de ipinfo.io.
     * 
     * @return void
     */
    private function _getIpInfo()
    {
        $details = $this->_executeCurl(
            "http://ipinfo.io/" . $this->_ip["address"] . "?token=" . IP_INFO_TOKEN
        );
        if (isset($details->bogon) or is_null($details)) {
            $this->_city["name"]    = "local";
            $this->_region["name"]  = "local";
            $this->_country["name"] = "local";
        } else {
            $this->_city["name"]    = $details->city;
            $this->_region["name"]  = $details->region;
            $this->_country["name"] = $details->country;
        }
    }
    /**
     * Verifica Pais
     * ------------------------------------------------------------------------------
     * Verifica si el pais existe en la base de datos, si no existe, lo guarda.
     * 
     * @return void
     */
    private function _checkCountry()
    {
        global $db;
        $db->where("name", $this->_country["name"]);
        $results = $db->get($this->_countrysTable);
        if (empty($results)) {
            $insertData
                = ["id" => null, "name" => $this->_country["name"]];
            $id = $db->insert($this->_countrysTable, $insertData);
        } else {
            $id = $results[0]["id"];
        }
        $this->_country["id"] = $id;
    }
    /**
     * Verifica region
     * ------------------------------------------------------------------------------
     * Verifica si el region existe en la base de datos, si no existe, lo guarda.
     * 
     * @return void
     */
    private function _checkRegion()
    {
        global $db;
        $db->where("name", $this->_region["name"]);
        $db->where("country_id", $this->_country["id"]);
        $results = $db->get($this->_regionsTable);
        if (empty($results)) {
            $insertData = [
                "id" => null,
                "country_id" => $this->_country["id"],
                "name" => $this->_region["name"]
            ];
            $id = $db->insert($this->_regionsTable, $insertData);
        } else {
            $id = $results[0]["id"];
        }
        $this->_region["id"]  = $id;
    }
    /**
     * Verifica region
     * ------------------------------------------------------------------------------
     * Verifica si el region existe en la base de datos, si no existe, lo guarda.
     * 
     * @return void
     */
    private function _checkCity()
    {
        global $db;
        $db->where("name", $this->_city["name"]);
        $db->where("region_id", $this->_region["id"]);
        $results = $db->get($this->_citysTable);
        if (empty($results)) {
            $insertData = [
                "id" => null,
                "region_id" => $this->_region["id"],
                "name" => $this->_city["name"]
            ];
            $id = $db->insert($this->_citysTable, $insertData);
        } else {
            $id = $results[0]["id"];
        }
        $this->_city["id"] = $id;
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
            "country_id"          => $this->_country["id"],
            "region_id"           => $this->_region["id"],
            "city_id"             => $this->_city["id"],
            "requested_uri_id"    => $this->_requestUri["id"],
            "referred_site_id"    => $this->_siteRefer["id"],
            "requested_method_id" => $this->_requestMethod["id"],
            "user_agent_id"       => $this->_userAgent["id"]
        ];
        if ($this->_requestUri["uri"] != "/favicon.ico") {
            $this->_lastIdSaved = $db->insert($this->_ipLogsTable, $insertData);
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
        $blacklist = $db->get($this->_ipsTable, null, "blacklisted");
        if (!empty($blacklist)) {
            exit("access denied\n");
        }
    }
    /**
     * Obtener Registro de Ips
     * ------------------------------------------------------------------------------
     * Obtiene todos los daltos almacenados de las direcciones ip registradas. Los
     * valores son obtenidos por paginas en caso de que la tabla sea muy grande.
     * 
     * @param int $pageIndex Pagina de resultados a motrar.
     * @param int $pageLimit Cantidad de resultados por pagina.
     * 
     * @return array
     */
    public function getLog($pageIndex = 1, $pageLimit = 10)
    {
        global $db;
        $db->pageLimit = $pageLimit;
        $ipsLog = $db->paginate($this->_ipLogsView, $pageIndex);
        return [$ipsLog, $db->totalPages];
    }
}
