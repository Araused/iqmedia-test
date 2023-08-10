CREATE DATABASE `shortlink-app` /*!40100 COLLATE 'utf8mb4_general_ci' */;
CREATE TABLE `link` (
                        `hash` VARCHAR(5) NOT NULL COLLATE 'utf8mb4_general_ci',
                        `landing` VARCHAR(255) NOT NULL COLLATE 'utf8mb4_general_ci',
                        `counter` INT(10) UNSIGNED NOT NULL DEFAULT '0',
                        PRIMARY KEY (`hash`) USING BTREE,
                        UNIQUE INDEX `hash` (`hash`) USING BTREE
)
    COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB
;
