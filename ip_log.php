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
 * -----------------------------------------------------------------------------
 * Clase para obtener la direccion ip del visitante, asi como todos los datos
 * relacionados a este, como el pais, el estado, la ciudad, el metodo de
 * soliicitud,  la url solicitudada, el sitio desde donde es referido y el user
 * agent.
 *
 * @category Class
 * @package  Ip_Log_Class
 * @author   R2D2DEV <r2d2dev@gmail.com>
 * @license  MIT License
 * @link     https://github.com/R2B2DEV
 */
class IpLog
{
    // Conexion a Base de Datos
    private $db                 = null;
    // Tables
    private $userAgentTable     = "ip_log_users_agent";
    private $countrysTable      = "ip_log_countrys";
    private $regionsTable       = "ip_log_regions";
    private $citysTable         = "ip_log_citys";
    private $ipLogsTable        = "ip_logs";
    private $ipLogsView         = "view_ip_logs";
    private $ipsTable           = "ip_log_ips";
    private $requestMethodTable = "ip_log_request_methods";
    private $requestUriTable    = "ip_log_request_uri";
    private $referredSiteTable  = "ip_log_referred_site";
    // Ip Data
    private $ip                 = ["id" => null, "address" => null];
    private $requestMethod      = ["id" => null, "method"  => null];
    private $requestUri         = ["id" => null, "uri"     => null];
    private $siteRefer          = ["id" => null, "site"    => null];
    private $userAgent          = ["id" => null, "name"    => null];
    private $country            = ["id" => null, "name"    => null];
    private $region             = ["id" => null, "name"    => null];
    private $city               = ["id" => null, "name"    => null];
    // Otros
    private $lastIdSaved        = null;
    /**
     * Funcion constructora
     * -------------------------------------------------------------------------
     * Construye -.-
     *
     * @return void
     */
    public function __construct()
    {
        $this->db                      = \MysqliDb::getInstance();
        $this->ip["address"]           = $_SERVER["REMOTE_ADDR"];
        $this->requestMethod["method"] = $_SERVER["REQUEST_METHOD"];
        $this->requestUri["uri"]       = $_SERVER["REQUEST_URI"];
        $this->siteRefer["site"]       = $_SERVER["HTTP_REFERER"] ?? "direct_connection";
        $this->userAgent               = ["id" => null, "name" => $_SERVER["HTTP_USER_AGENT"]];
        $this->checkUserAgent();
        $this->checkIp();
        $this->checkRequestMethod();
        $this->checkRequestUri();
        $this->checkReferredSite();
        $this->getIpInfo();
        $this->checkCountry();
        $this->checkRegion();
        $this->checkCity();
    }
    /**
     * Ejecuta Curl
     * -------------------------------------------------------------------------
     * Ejecuta Curl con parametros GET
     *
     * @param string $url url para curl
     *
     * @return object
     */
    private function executeCurl($url)
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
     * -------------------------------------------------------------------------
     * Verifica si el User Agent existe en la base de datos, si no existe, lo guarda.
     *
     * @return void
     */
    private function checkUserAgent()
    {
        $this->db->where("name", $this->userAgent["name"]);
        $results = $this->db->get($this->userAgentTable);
        if (empty($results)) {
            $insertData = ["id" => null, "name" => $this->userAgent["name"]];
            $id = $this->db->insert($this->userAgentTable, $insertData);
        } else {
            $id = $results[0]["id"];
        }
        $this->userAgent["id"] = $id;
    }
    /**
     * Verifica IP
     * -------------------------------------------------------------------------
     * Verifica si la IP existe en la base de datos, si no existe, la guarda.
     *
     * @return void
     */
    private function checkIp()
    {
        $this->db->where("address", $this->ip["address"]);
        $results = $this->db->get($this->ipsTable);
        if (empty($results)) {
            $insertData = ["id" => null, "address" => $this->ip["address"]];
            $id = $this->db->insert($this->ipsTable, $insertData);
        } else {
            $id = $results[0]["id"];
        }
        $this->ip["id"] = $id;
    }
    /**
     * Verifica Metodo de Solicitud
     * -------------------------------------------------------------------------
     * Verifica si la Metodo de Solicitud existe en la base de datos, si no
     * existe, *  la guarda.
     *
     * @return void
     */
    private function checkRequestMethod()
    {
        $this->db->where("method", $this->requestMethod["method"]);
        $results = $this->db->get($this->requestMethodTable);
        if (empty($results)) {
            $insertData = ["id" => null, "method" => $this->requestMethod["method"]];
            $id = $this->db->insert($this->requestMethodTable, $insertData);
        } else {
            $id = $results[0]["id"];
        }
        $this->requestMethod["id"] = $id;
    }
    /**
     * Verifica URI solicitada
     * -------------------------------------------------------------------------
     * Verifica si la URI solicitada existe en la base de datos, si no existe,
     *  la guarda.
     *
     * @return void
     */
    private function checkRequestUri()
    {
        $this->db->where("uri", $this->requestUri["uri"]);
        $results = $this->db->get($this->requestUriTable);
        if (empty($results)) {
            $insertData = ["id" => null, "uri" => $this->requestUri["uri"]];
            $id = $this->db->insert($this->requestUriTable, $insertData);
        } else {
            $id = $results[0]["id"];
        }
        $this->requestUri["id"] = $id;
    }
    /**
     * Verifica Sitio Referido
     * -------------------------------------------------------------------------
     * Verifica si el Sitio Referido existe en la base de datos, si no existe,
     *  la crea.
     *
     * @return void
     */
    private function checkReferredSite()
    {
        $this->db->where("site", $this->siteRefer["site"]);
        $results = $this->db->get($this->referredSiteTable);
        if (empty($results)) {
            $insertData = ["id" => null, "site" => $this->siteRefer["site"]];
            $id = $this->db->insert($this->referredSiteTable, $insertData);
        } else {
            $id = $results[0]["id"];
        }
        $this->siteRefer["id"] = $id;
    }
    /**
     * Obtener informacion de la IP
     * -------------------------------------------------------------------------
     * Obtiene informacion de la direccion ip del cliente a traves de ipinfo.io.
     *
     * @return void
     */
    private function getIpInfo()
    {
        $details = $this->executeCurl("http://ipinfo.io/" . $this->ip["address"] . "?token=" . IP_INFO_TOKEN);
        if (isset($details->bogon) or is_null($details)) {
            $this->city["name"]    = "local";
            $this->region["name"]  = "local";
            $this->country["name"] = "local";
        } else {
            $this->city["name"]    = $details->city;
            $this->region["name"]  = $details->region;
            $this->country["name"] = $details->country;
        }
    }
    /**
     * Verifica Pais
     * -------------------------------------------------------------------------
     * Verifica si el pais existe en la base de datos, si no existe, lo guarda.
     *
     * @return void
     */
    private function checkCountry()
    {
        $this->db->where("name", $this->country["name"]);
        $results = $this->db->get($this->countrysTable);
        if (empty($results)) {
            $insertData = ["id" => null, "name" => $this->country["name"]];
            $id = $this->db->insert($this->countrysTable, $insertData);
        } else {
            $id = $results[0]["id"];
        }
        $this->country["id"] = $id;
    }
    /**
     * Verifica region
     * -------------------------------------------------------------------------
     * Verifica si el region existe en la base de datos, si no existe, lo guarda.
     *
     * @return void
     */
    private function checkRegion()
    {
        $this->db->where("name", $this->region["name"]);
        $this->db->where("country_id", $this->country["id"]);
        $results = $this->db->get($this->regionsTable);
        if (empty($results)) {
            $insertData = [
                "id" => null,
                "country_id" => $this->country["id"],
                "name" => $this->region["name"]
            ];
            $id = $this->db->insert($this->regionsTable, $insertData);
        } else {
            $id = $results[0]["id"];
        }
        $this->region["id"]  = $id;
    }
    /**
     * Verifica region
     * -------------------------------------------------------------------------
     * Verifica si el region existe en la base de datos, si no existe, lo guarda.
     *
     * @return void
     */
    private function checkCity()
    {
        $this->db->where("name", $this->city["name"]);
        $this->db->where("region_id", $this->region["id"]);
        $results = $this->db->get($this->citysTable);
        if (empty($results)) {
            $insertData = [
                "id" => null,
                "region_id" => $this->region["id"],
                "name" => $this->city["name"]
            ];
            $id = $this->db->insert($this->citysTable, $insertData);
        } else {
            $id = $results[0]["id"];
        }
        $this->city["id"] = $id;
    }
    /**
     * Guardar registro
     * -------------------------------------------------------------------------
     * Guarda todos los datos del registro en la base de datos.
     *
     * @return void
     */
    public function saveLog()
    {
        $insertData = [
            "id"                  => null,
            "date"                => $this->db->now(),
            "ip_id"               => $this->ip["id"],
            "user_id"             => null,
            "country_id"          => $this->country["id"],
            "region_id"           => $this->region["id"],
            "city_id"             => $this->city["id"],
            "requested_uri_id"    => $this->requestUri["id"],
            "referred_site_id"    => $this->siteRefer["id"],
            "requested_method_id" => $this->requestMethod["id"],
            "user_agent_id"       => $this->userAgent["id"]
        ];
        if ($this->requestUri["uri"] != "/favicon.ico") {
            $this->_lastIdSaved = $this->db->insert($this->ipLogsTable, $insertData);
        }
    }
    /**
     * Denegar Acceso
     * -------------------------------------------------------------------------
     * Verifica si la direccio ip esta en la lista negra y restringe el acceso.
     *
     * @return void
     */
    public function denyAccess()
    {
        $this->db->where("address", $this->ip["address"]);
        $this->db->where("blacklisted", true);
        $blacklist = $this->db->get($this->ipsTable, null, "blacklisted");
        if (!empty($blacklist)) {
            exit("access denied\n");
        }
    }
    /**
     * Obtener Registro de Ips
     * -------------------------------------------------------------------------
     * Obtiene todos los daltos almacenados de las direcciones ip registradas.
     * Los valores son obtenidos por paginas en caso de que la tabla sea muy
     * grande.
     *
     * @param int $pageIndex Pagina de resultados a motrar.
     * @param int $pageLimit Cantidad de resultados por pagina.
     *
     * @return array
     */
    public function getLog($pageIndex = 1, $pageLimit = 10)
    {
        $this->db->pageLimit = $pageLimit;
        $ipsLog = $this->db->paginate($this->ipLogsView, $pageIndex);
        return [$ipsLog, $this->db->totalPages];
    }
}
