<?php
/**
 * @package Sefab Plugin
 */
namespace Inc\Managers;

class DbManager
{
    public static function create($tableName, $columns)
    {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        $primary = "";
        $sql = "CREATE TABLE $tableName (";
        foreach ($columns as $column) {
            if ($column['isPrimary']) {
                $primary = $column['name'];
            }
            $sql .= $column['name'] . " " . $column['type'] . " " . $column['isNull'] . " " . $column['extra'] . ", ";
        }
        $sql.=" PRIMARY KEY ($primary)) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public static function insert($table_name, array $column_values)
    {
        global $wpdb;

        $wpdb->insert($table_name, $column_values);
        
        return $wpdb->insert_id;
    }

    public static function select($columns, $tables, $conditions, $extras = '')
    {
        global $wpdb;
        $query = "SELECT $columns FROM $tables WHERE $conditions $extras";

        return $wpdb->get_results($query);
    }

    public function update($table, $values, $conditions, $extras = '')
    {
        global $wpdb;
        $query = "UPDATE $table SET $values WHERE $conditions $extras";
    
        return $wpdb->query($query);
    }

    public function replace_user_search_query($user_search)
    {
        global $wpdb;
    
        $user_search->query_where = str_replace(
            'WHERE 1=1',
            "WHERE 1=1 AND {$wpdb->users}.ID IN (
              SELECT {$wpdb->usermeta}.user_id FROM $wpdb->usermeta 
              WHERE {$wpdb->usermeta}.meta_key = '{$wpdb->prefix}capabilities'
              AND {$wpdb->usermeta}.meta_value LIKE '%employee%' OR {$wpdb->usermeta}.meta_value LIKE '%lower_administrator%')",
            $user_search->query_where
        );
    }

    public function select_from_users_table($columns, $conditions, $extras = '')
    {
        global $wpdb;

        $query = "SELECT $columns FROM $wpdb->users WHERE $conditions $extras";
        
        return $wpdb->get_results($query);
    }

    public static function wp_update($tableName, array $toUpdate, $condition)
    {
        global $wpdb;
        $wpdb->update($tableName, $toUpdate, $condition);
    }

    public static function delete($tableName, $condition)
    {
        global $wpdb;
        $wpdb->delete($tableName, $condition);
    }
}
