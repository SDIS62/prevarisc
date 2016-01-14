SET NAMES 'utf8';

/* Enables mysql memcache plugin */
/* Path to mysql cache plugin depends on your installation */
SOURCE '/usr/share/mysql/innodb_memcached_config.sql';
INSTALL PLUGIN daemon_memcached SONAME 'libmemcached.so';

/* Declares container configuration and clean previous one*/
USE innodb_memcache;

DELETE FROM containers;
INSERT INTO `containers` (`name`, `db_schema`, `db_table`, `key_columns`, `value_columns`, `flags`, `cas_column`, `expire_time_column`, `unique_idx_name_on_key`)
VALUES ('prevarisc_cache', 'prevarisc', 'cache', 'ID_CACHE', 'VALUE_CACHE', 0, 0, 'EXPIRE_CACHE', 'PRIMARY');
