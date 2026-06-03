<?php
/**
 * MyProtector Platform - Base Model
 * 
 * Abstract base class for all database models
 * 
 * @package MyProtector\Models
 * @version 1.0.0
 */

namespace MyProtector\Models;

abstract class BaseModel {
    /**
     * WordPress database object
     * 
     * @var \wpdb
     */
    protected $wpdb;

    /**
     * Table name (without prefix)
     * 
     * @var string
     */
    protected $table;

    /**
     * Primary key column name
     * 
     * @var string
     */
    protected $primary_key = 'id';

    /**
     * Auto-increment flag
     * 
     * @var bool
     */
    protected $auto_increment = true;

    /**
     * Cache group for transients
     * 
     * @var string
     */
    protected $cache_group = 'mp_models';

    /**
     * Cache TTL in seconds
     * 
     * @var int
     */
    protected $cache_ttl = 3600;

    /**
     * Constructor
     */
    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    /**
     * Get full table name with prefix
     * 
     * @return string
     */
    protected function getTableName(): string {
        return $this->wpdb->prefix . $this->table;
    }

    /**
     * Get a single record by ID
     * 
     * @param int $id
     * @return object|null
     */
    public function get($id) {
        $cache_key = $this->getCacheKey($id);
        $cached = $this->getCache($cache_key);
        
        if ($cached !== false) {
            return $cached;
        }

        $sql = $this->wpdb->prepare(
            "SELECT * FROM {$this->getTableName()} WHERE {$this->primary_key} = %d",
            $id
        );
        
        $result = $this->wpdb->get_row($sql);
        
        if ($result) {
            $this->setCache($cache_key, $result);
        }
        
        return $result;
    }

    /**
     * Get a row by a specific column value
     * 
     * @param string $column
     * @param mixed $value
     * @return object|null
     */
    public function getBy(string $column, $value) {
        $sql = $this->wpdb->prepare(
            "SELECT * FROM {$this->getTableName()} WHERE {$column} = %s LIMIT 1",
            $value
        );
        
        return $this->wpdb->get_row($sql);
    }

    /**
     * Get all records
     * 
     * @param array $args Optional arguments (where, orderby, order, limit, offset)
     * @return array
     */
    public function getAll(array $args = []): array {
        $defaults = [
            'where' => [],
            'orderby' => $this->primary_key,
            'order' => 'DESC',
            'limit' => 100,
            'offset' => 0,
        ];
        
        $args = wp_parse_args($args, $defaults);
        extract($args);

        $where_sql = '';
        $values = [];

        if (!empty($where)) {
            $conditions = [];
            foreach ($where as $column => $value) {
                if (is_null($value)) {
                    $conditions[] = "{$column} IS NULL";
                } elseif (is_array($value)) {
                    $placeholders = implode(',', array_fill(0, count($value), '%s'));
                    $conditions[] = "{$column} IN ({$placeholders})";
                    $values = array_merge($values, $value);
                } else {
                    $conditions[] = "{$column} = %s";
                    $values[] = $value;
                }
            }
            $where_sql = 'WHERE ' . implode(' AND ', $conditions);
        }

        $limit = absint($limit);
        $offset = absint($offset);
        $order = sanitize_sql_orderby("{$orderby} {$order}");
        
        $sql = "SELECT * FROM {$this->getTableName()} {$where_sql} ORDER BY {$order} LIMIT {$limit} OFFSET {$offset}";
        
        if (!empty($values)) {
            $sql = $this->wpdb->prepare($sql, $values);
        }
        
        return $this->wpdb->get_results($sql);
    }

    /**
     * Insert a new record
     * 
     * @param array $data
     * @return int|false
     */
    public function insert(array $data) {
        // Add timestamps
        if (!isset($data['created_at'])) {
            $data['created_at'] = current_time('mysql');
        }
        
        if (!isset($data['updated_at'])) {
            $data['updated_at'] = current_time('mysql');
        }

        $result = $this->wpdb->insert(
            $this->getTableName(),
            $data,
            $this->getFormat($data)
        );

        if ($result === false) {
            return false;
        }

        $insert_id = $this->auto_increment ? $this->wpdb->insert_id : 0;
        
        $this->clearCache();
        
        return $insert_id;
    }

    /**
     * Update a record
     * 
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, array $data): bool {
        $data['updated_at'] = current_time('mysql');
        
        $result = $this->wpdb->update(
            $this->getTableName(),
            $data,
            [$this->primary_key => $id],
            $this->getFormat($data),
            ['%d']
        );

        if ($result === false) {
            return false;
        }

        $this->clearCache($id);
        
        return true;
    }

    /**
     * Delete a record
     * 
     * @param int $id
     * @return bool
     */
    public function delete($id): bool {
        $result = $this->wpdb->delete(
            $this->getTableName(),
            [$this->primary_key => $id],
            ['%d']
        );

        if ($result === false) {
            return false;
        }

        $this->clearCache($id);
        
        return true;
    }

    /**
     * Count records
     * 
     * @param array $where
     * @return int
     */
    public function count(array $where = []): int {
        $where_sql = '';
        $values = [];

        if (!empty($where)) {
            $conditions = [];
            foreach ($where as $column => $value) {
                if (is_null($value)) {
                    $conditions[] = "{$column} IS NULL";
                } else {
                    $conditions[] = "{$column} = %s";
                    $values[] = $value;
                }
            }
            $where_sql = 'WHERE ' . implode(' AND ', $conditions);
        }

        $sql = "SELECT COUNT(*) FROM {$this->getTableName()} {$where_sql}";
        
        if (!empty($values)) {
            $sql = $this->wpdb->prepare($sql, $values);
        }
        
        return (int) $this->wpdb->get_var($sql);
    }

    /**
     * Check if record exists
     * 
     * @param int $id
     * @return bool
     */
    public function exists($id): bool {
        $sql = $this->wpdb->prepare(
            "SELECT COUNT(*) FROM {$this->getTableName()} WHERE {$this->primary_key} = %d",
            $id
        );
        
        return (int) $this->wpdb->get_var($sql) > 0;
    }

    /**
     * Query with raw SQL
     * 
     * @param string $sql
     * @param array $args
     * @return array
     */
    public function query(string $sql, array $args = []): array {
        if (!empty($args)) {
            $sql = $this->wpdb->prepare($sql, $args);
        }
        
        return $this->wpdb->get_results($sql);
    }

    /**
     * Get a single value
     * 
     * @param string $sql
     * @param array $args
     * @return mixed
     */
    public function getVar(string $sql, array $args = []) {
        if (!empty($args)) {
            $sql = $this->wpdb->prepare($sql, $args);
        }
        
        return $this->wpdb->get_var($sql);
    }

    /**
     * Get format array for wpdb::insert/update
     * 
     * @param array $data
     * @return array
     */
    protected function getFormat(array $data): array {
        $format = [];
        foreach ($data as $value) {
            if (is_int($value)) {
                $format[] = '%d';
            } elseif (is_float($value)) {
                $format[] = '%f';
            } elseif (is_null($value)) {
                $format[] = 'NULL';
            } else {
                $format[] = '%s';
            }
        }
        return $format;
    }

    /**
     * Get cache key
     * 
     * @param mixed $id
     * @return string
     */
    protected function getCacheKey($id): string {
        return $this->cache_group . '_' . $this->table . '_' . $id;
    }

    /**
     * Get cached value
     * 
     * @param string $key
     * @return mixed
     */
    protected function getCache(string $key) {
        return wp_cache_get($key, $this->cache_group);
    }

    /**
     * Set cached value
     * 
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    protected function setCache(string $key, $value): bool {
        return wp_cache_set($key, $value, $this->cache_group, $this->cache_ttl);
    }

    /**
     * Clear cache
     * 
     * @param mixed $id Optional specific ID
     * @return void
     */
    protected function clearCache($id = null): void {
        if ($id !== null) {
            wp_cache_delete($this->getCacheKey($id), $this->cache_group);
        } else {
            wp_cache_flush();
        }
    }

    /**
     * Sanitize input
     * 
     * @param mixed $value
     * @return mixed
     */
    protected function sanitize($value) {
        if (is_array($value)) {
            return array_map([$this, 'sanitize'], $value);
        }
        
        if (is_string($value)) {
            return sanitize_text_field($value);
        }
        
        return $value;
    }

    /**
     * Get last error
     * 
     * @return string
     */
    public function getLastError(): string {
        return $this->wpdb->last_error;
    }

    /**
     * Get last query
     * 
     * @return string
     */
    public function getLastQuery(): string {
        return $this->wpdb->last_query;
    }
}