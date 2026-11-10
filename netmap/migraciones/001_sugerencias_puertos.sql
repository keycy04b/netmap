-- NetMap: presets administrables de grupos y cantidades de puertos.
-- Ejecutar una sola vez en la base de datos netmap.
CREATE TABLE IF NOT EXISTS sugerencias_puertos (
    id_sugerencia INT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_tipo_equipo INT NOT NULL,
    alcance ENUM('LAN','WAN') NOT NULL DEFAULT 'LAN',
    medio ENUM('UTP','FIBRA') NOT NULL DEFAULT 'UTP',
    cantidad SMALLINT UNSIGNED NOT NULL,
    secciones SMALLINT UNSIGNED NOT NULL DEFAULT 1,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (id_sugerencia),
    UNIQUE KEY uq_sugerencia_tipo (id_tipo_equipo, alcance, medio, cantidad),
    KEY idx_sugerencia_tipo_activo (id_tipo_equipo, activo),
    CONSTRAINT fk_sugerencias_tipo
        FOREIGN KEY (id_tipo_equipo) REFERENCES tipos_equipo(id_tipo_equipo)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT IGNORE INTO sugerencias_puertos (id_tipo_equipo, alcance, medio, cantidad, secciones)
SELECT id_tipo_equipo, 'LAN', 'UTP', 8, 1 FROM tipos_equipo
WHERE LOWER(nombre_tipo) REGEXP 'switch|swiche|conmutador';
INSERT IGNORE INTO sugerencias_puertos (id_tipo_equipo, alcance, medio, cantidad, secciones)
SELECT id_tipo_equipo, 'LAN', 'UTP', 16, 2 FROM tipos_equipo
WHERE LOWER(nombre_tipo) REGEXP 'switch|swiche|conmutador';
INSERT IGNORE INTO sugerencias_puertos (id_tipo_equipo, alcance, medio, cantidad, secciones)
SELECT id_tipo_equipo, 'LAN', 'UTP', 24, 2 FROM tipos_equipo
WHERE LOWER(nombre_tipo) REGEXP 'switch|swiche|conmutador';
INSERT IGNORE INTO sugerencias_puertos (id_tipo_equipo, alcance, medio, cantidad, secciones)
SELECT id_tipo_equipo, 'LAN', 'UTP', 48, 4 FROM tipos_equipo
WHERE LOWER(nombre_tipo) REGEXP 'switch|swiche|conmutador';
