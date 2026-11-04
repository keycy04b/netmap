# Migraciones de NetMap

## 001_sugerencias_puertos.sql

Esta migración crea la tabla `sugerencias_puertos` y carga presets iniciales de puertos para los tipos de equipo cuyo nombre corresponde a switch, swiche o conmutador.

### Cómo aplicarla

1. Haz una copia de seguridad de la base de datos `netmap`.
2. Abre phpMyAdmin y selecciona la base de datos `netmap`.
3. Entra en la pestaña **SQL**.
4. Abre el archivo `001_sugerencias_puertos.sql`, copia todo su contenido y ejecútalo.
5. Comprueba que aparece la tabla `sugerencias_puertos`.
6. Abre NetMap → **Presets** para administrar tipos y sugerencias.

La migración no elimina ni modifica los equipos, puertos o enlaces existentes. Ejecuta el script una sola vez; usa `CREATE TABLE IF NOT EXISTS` e `INSERT IGNORE` para evitar duplicar la tabla y los presets iniciales.
