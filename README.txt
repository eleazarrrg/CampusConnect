# Campus Connect UTP — v3

## Instalación (WAMP + phpMyAdmin)
1) Copia `campus-connect-v3` a `C:\wamp64\www\`.
2) Abre phpMyAdmin → pestaña **SQL** → pega el contenido de `schema.sql` (incluido).
3) Navega a `http://localhost/campus-connect-v3/` (o la carpeta que uses).

## Notas
- El `layout_top.php` usa **base dinámico**, no depende de `config.php`.
- Todos los endpoints que mutan verifican **CSRF** y **sesión 30 min**.
- “Correos” simulados en `/outbox`.
