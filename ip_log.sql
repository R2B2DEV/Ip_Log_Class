-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `tu_ip_log_users_agent`
--
DROP TABLE IF EXISTS `tu_ip_log_users_agent`;
CREATE TABLE IF NOT EXISTS `tu_ip_log_users_agent` (
    `id` BIGINT NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL UNIQUE,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB;
-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `tu_ip_log_ips`
--
DROP TABLE IF EXISTS `tu_ip_log_ips`;
CREATE TABLE IF NOT EXISTS `tu_ip_log_ips` (
    `id` BIGINT NOT NULL AUTO_INCREMENT,
    `address` VARCHAR(255) NOT NULL UNIQUE,
    `whitelisted` BOOLEAN NOT NULL,
    `blacklisted` BOOLEAN NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB;
-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `tu_ip_log_request_methods`
--
DROP TABLE IF EXISTS `tu_ip_log_request_methods`;
CREATE TABLE IF NOT EXISTS `tu_ip_log_request_methods` (
    `id` BIGINT NOT NULL AUTO_INCREMENT,
    `method` VARCHAR(255) NOT NULL UNIQUE,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB;
-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `tu_ip_log_request_uri`
--
DROP TABLE IF EXISTS `tu_ip_log_request_uri`;
CREATE TABLE IF NOT EXISTS `tu_ip_log_request_uri` (
    `id` BIGINT NOT NULL AUTO_INCREMENT,
    `uri` VARCHAR(255) NOT NULL UNIQUE,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB;
-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `tu_ip_log_referred_site`
--
DROP TABLE IF EXISTS `tu_ip_log_referred_site`;
CREATE TABLE IF NOT EXISTS `tu_ip_log_referred_site` (
    `id` BIGINT NOT NULL AUTO_INCREMENT,
    `site` VARCHAR(255) NOT NULL UNIQUE,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB;
-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `tu_ip_log_countrys`
--
DROP TABLE IF EXISTS `tu_ip_log_countrys`;
CREATE TABLE IF NOT EXISTS `tu_ip_log_countrys` (
    `id` BIGINT NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL UNIQUE,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB;
-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `tu_ip_log_regions`
--
DROP TABLE IF EXISTS `tu_ip_log_regions`;
CREATE TABLE IF NOT EXISTS `tu_ip_log_regions` (
    `id` BIGINT NOT NULL AUTO_INCREMENT,
    `country_id` BIGINT NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`country_id`) REFERENCES `tu_ip_log_countrys`(`id`)
) ENGINE = InnoDB;
-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `tu_ip_log_citys`
--
DROP TABLE IF EXISTS `tu_ip_log_citys`;
CREATE TABLE IF NOT EXISTS `tu_ip_log_citys` (
    `id` BIGINT NOT NULL AUTO_INCREMENT,
    `region_id` BIGINT NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`region_id`) REFERENCES `tu_ip_log_regions`(`id`)
) ENGINE = InnoDB;
-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `tu_ip_logs`
--
DROP TABLE IF EXISTS `tu_ip_logs`;
CREATE TABLE IF NOT EXISTS `tu_ip_logs` (
    `id` BIGINT NOT NULL AUTO_INCREMENT,
    `date` TIMESTAMP NOT NULL,
    `ip_id` BIGINT NOT NULL,
    `user_id` BIGINT,
    `country_id` BIGINT NOT NULL,
    `region_id` BIGINT NOT NULL,
    `city_id` BIGINT NOT NULL,
    `requested_uri_id` BIGINT NOT NULL,
    `referred_site_id` BIGINT NOT NULL,
    `requested_method_id` BIGINT NOT NULL,
    `user_agent_id` BIGINT NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`ip_id`) REFERENCES `tu_ip_log_ips`(`id`),
    FOREIGN KEY (`country_id`) REFERENCES `tu_ip_log_countrys`(`id`),
    FOREIGN KEY (`region_id`) REFERENCES `tu_ip_log_regions`(`id`),
    FOREIGN KEY (`city_id`) REFERENCES `tu_ip_log_citys`(`id`),
    FOREIGN KEY (`requested_uri_id`) REFERENCES `tu_ip_log_request_uri`(`id`),
    FOREIGN KEY (`referred_site_id`) REFERENCES `tu_ip_log_referred_site`(`id`),
    FOREIGN KEY (`requested_method_id`) REFERENCES `tu_ip_log_request_methods`(`id`),
    FOREIGN KEY (`user_agent_id`) REFERENCES `tu_ip_log_users_agent`(`id`)
) ENGINE = InnoDB;
-- --------------------------------------------------------
--
-- Estructura para la vista `tu_view_ip_logs`
--
DROP VIEW IF EXISTS `tu_view_ip_logs`;
CREATE VIEW `tu_view_ip_logs` as
SELECT `tu_ip_logs`.`id`,
    `date`,
    `tu_ip_log_ips`.`address` as `ip`,
    `user_id`,
    `tu_ip_log_countrys`.`name` as `country`,
    `tu_ip_log_regions`.`name` as `region`,
    `tu_ip_log_citys`.`name` as `city`,
    `tu_ip_log_request_uri`.`uri` as `requested_uri`,
    `tu_ip_log_referred_site`.`site` as `refered_site_uri`,
    `tu_ip_log_request_methods`.`method` as `requested_method`,
    `tu_ip_log_users_agent`.`name` as `user_agent`
FROM `tu_ip_logs`
    LEFT JOIN `tu_ip_log_ips` ON `tu_ip_log_ips`.`id` = `ip_id`
    LEFT JOIN `tu_ip_log_countrys` ON `tu_ip_log_countrys`.`id` = `country_id`
    LEFT JOIN `tu_ip_log_regions` ON `tu_ip_log_regions`.`id` = `region_id`
    LEFT JOIN `tu_ip_log_citys` ON `tu_ip_log_citys`.`id` = `city_id`
    LEFT JOIN `tu_ip_log_request_uri` ON `tu_ip_log_request_uri`.`id` = `requested_uri_id`
    LEFT JOIN `tu_ip_log_referred_site` ON `tu_ip_log_referred_site`.`id` = `referred_site_id`
    LEFT JOIN `tu_ip_log_request_methods` ON `tu_ip_log_request_methods`.`id` = `requested_method_id`
    LEFT JOIN `tu_ip_log_users_agent` ON `tu_ip_log_users_agent`.`id` = `user_agent_id`
ORDER BY `tu_ip_logs`.`id` DESC;