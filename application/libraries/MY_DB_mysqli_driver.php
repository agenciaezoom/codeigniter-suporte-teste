<?php

class MY_DB_mysqli_driver extends CI_DB_mysqli_driver
{
    private $soft_delete_origin = false;
    private $not = array(
        'ez_session'
    );

    public function __construct($params)
    {
        parent::__construct($params);
        log_message('debug', 'Extended DB driver class instantiated!');
        if ($this->check_soft_delete()) {
            $this->soft_delete_origin = $this->soft_delete;
            if ($this->soft_delete) {
                log_message('debug', 'Soft Delete Ativado!');
            }
        }
    }

    /**
     * Verifica se as configurações para o soft delete existe.
     * @author Ramon Barros <ramon@ezoom.com.br>
     * @date      2015-07-31
     * @copyright Copyright  (c)           2015, Ezoom
     * @return    void
     */
    private function check_soft_delete() {
        return property_exists($this, 'soft_delete') &&
               property_exists($this, 'soft_delete_type') &&
               property_exists($this, 'soft_delete_column') &&
               property_exists($this, 'soft_delete_value') &&
               property_exists($this, 'soft_delete_check_column');
    }

    /**
     * Altera os where para que a regra do soft delete seja inserida.
     * @author Ramon Barros <ramon@ezoom.com.br>
     * @date      2015-07-31
     * @copyright Copyright  (c)           2015, Ezoom
     * @return    void
     */
    private function _alter_where()
    {
        $ar_where = array();
        if (!empty($this->ar_where)) {
            $ar_where[] = '('.implode(' ', $this->ar_where) . ')';
            $this->ar_where = $ar_where;
        }
        $ar_like = array();
        if (!empty($this->ar_like)) {
            $ar_like[] = '('.implode(' ', $this->ar_like) . ')';
            $this->ar_like = $ar_like;
        }
        $ar_wherein = array();
        if (!empty($this->ar_wherein)) {
            $ar_wherein[] = '('.implode(' ', $this->ar_wherein) . ')';
            $this->ar_wherein = $ar_wherein;
        }
    }

    /**
     * Se ativo verifica se a coluna do soft delete existe na tabela.
     * @author Ramon Barros <ramon@ezoom.com.br>
     * @date      2015-07-31
     * @copyright Copyright  (c)    2015,         Ezoom
     * @param     string     $table
     * @return    boolean
     */
    private function _check_column($table = '')
    {
        if ($this->soft_delete_check_column == true) {
            return $this->field_exists($this->soft_delete_column, $table);
        }
        return true;
    }

    /**
     * Recupera o alias utilizado na tabela
     * @author Ramon Barros [ramon@ezoom.com.br]
     * @date   2015-03-27
     * @param  string $table
     * @return string
     */
    private function _table_alias($table = '')
    {
        if (preg_match('@(?:\sas)?\s([\w\W]+)$@', $table, $match)) {
            return str_replace('as', '', end($match));
        } else {
            return $table;
        }

        return false;
    }

    /**
     * Recupera o nome da tabela mesmo com alias
     * table t = table
     * database.table t = table
     * database.`table` t = `table`
     * `database`.`table` `t` = `table`
     *
     * @author Ramon Barros [ramon@ezoom.com.br]
     * @date   2015-03-27
     * @param  string $table
     * @return string
     */
    private function _table_name($table = '')
    {
        if (preg_match('@[\.]?([\w-_`]+)\s{1}?@', $table, $match)) {
            return end($match);
        }
        return $table;
    }

    public function table($table = '')
    {
        if ($alias = $this->_table_alias($table)) {
            return $alias;
        } else {
            return $this->_table_name($table);
        }
    }

    public function soft_delete_reset($table = null)
    {
        if (!in_array($table, $this->not)) {
            $this->soft_delete = $this->soft_delete_origin;
        }
        return $this;
    }

    /**
     * Adiciona where do soft delete nas consultas.
     * @author Ramon Barros <ramon@ezoom.com.br>
     * @date      2015-07-31
     * @copyright Copyright  (c)    2015,         Ezoom
     * @param     string     $table
     * @return    MY_DB_mysql_driver
     */
    public function soft_delete($table = null) {
        if (is_bool($table)) {
            $this->soft_delete = $table;
        }
        if ($this->check_soft_delete() && $this->soft_delete && is_string($table) && !in_array($table, $this->not)) {
            $tables = !empty($table) ? array($table) : $this->ar_from;
            foreach ($tables as $key => $table) {
                if ($this->_check_column($table)) {
                    $this->_alter_where();
                    if ($this->soft_delete_type == 'boolean') {
                        $this->where($this->table($table) .  '.' . $this->_protect_identifiers($this->soft_delete_column), (bool)$this->soft_delete_value);
                    } elseif ($this->soft_delete_type == 'date' && is_bool($this->soft_delete_value)) {
                        $this->where($this->table($table) .  '.' . $this->_protect_identifiers($this->soft_delete_column) . ' IS NULL', null, false);
                    } else {
                        $this->where($this->table($table) .  '.' . $this->_protect_identifiers($this->soft_delete_column) . ' <=', $this->soft_delete_value);
                    }
                } else {
                    log_message('error', 'Coluna '.$this->soft_delete_column.' não existe na tabela '.$this->table($table).'!');
                }
            }
        }
        return $this;
    }

    /**
     * Get
     *
     * Compiles the select statement based on the other functions called
     * and runs the query
     *
     * @param   string  the table
     * @param   string  the limit clause
     * @param   string  the offset clause
     * @return  object
     */
    public function get($table = '', $limit = null, $offset = null)
    {
        $this->soft_delete($table);
        $get = parent::get($table, $limit, $offset);
        $this->soft_delete_reset($table);
        return $get;
    }

    /**
     * "Count All Results" query
     *
     * Generates a platform-specific query string that counts all records
     * returned by an Active Record query.
     *
     * @param   string
     * @return  string
     */
    public function count_all_results($table = '')
    {
        $this->soft_delete($table);
        $count_all_results = parent::count_all_results($table);
        $this->soft_delete_reset($table);
        return $count_all_results;
    }

    /**
     * Get_Where
     *
     * Allows the where clause, limit and offset to be added directly
     *
     * @param   string  the where clause
     * @param   string  the limit clause
     * @param   string  the offset clause
     * @return  object
     */
    public function get_where($table = '', $where = null, $limit = null, $offset = null)
    {
        $this->soft_delete($table);
        $get_where = parent::get_where($table, $where, $limit, $offset);
        $this->soft_delete_reset($table);
        return $get_where;
    }

    /**
     * Delete
     *
     * Compiles a delete string and runs the query
     *
     * @param   mixed   the table(s) to delete from. String or array
     * @param   mixed   the where clause
     * @param   mixed   the limit clause
     * @param   boolean
     * @return  object
     */
    public function delete($table = '', $where = '', $limit = null, $reset_data = true)
    {
        if ($this->check_soft_delete() && $this->soft_delete && is_string($table) && !in_array($table, $this->not)) {
            $delete = array();
            $tables = !empty($table) ? array($table) : $this->ar_from;
            foreach ($tables as $key => $table) {
                if ($this->field_exists($this->soft_delete_column, $table)) {
                    if ($this->soft_delete_type == 'boolean') {
                        $set = array( $this->soft_delete_column => true );
                    } elseif ($this->soft_delete_type == 'date') {
                        $set = array( $this->soft_delete_column => date('Y-m-d H:i:s'));
                    }
                    $delete[] = parent::update($table, $set, $where, $limit);
                } else {
                    log_message('error', 'Coluna '.$this->soft_delete_column.' não existe na tabela '.$table.'!');
                }
            }
            return !array_search(false, $delete);
        } else {
            return parent::delete($table, $where, $limit, $reset_data);
        }
    }

    /**
     * Empty Table
     *
     * Compiles a delete string and runs "DELETE FROM table"
     *
     * @param   string  the table to empty
     * @return  object
     */
    public function empty_table($table = '')
    {
        if ($this->check_soft_delete() && $this->soft_delete && is_string($table) && !in_array($table, $this->not)) {
            $delete = array();
            $tables = !empty($table) ? array($table) : $this->ar_from;
            foreach ($tables as $key => $table) {
                if ($this->field_exists($this->soft_delete_column, $table)) {
                    if ($this->soft_delete_type == 'boolean') {
                        $set = array( $this->soft_delete_column => true );
                    } elseif ($this->soft_delete_type == 'date') {
                        $set = array( $this->soft_delete_column => date('Y-m-d H:i:s'));
                    }
                    $delete[] = parent::update($table, $set);
                } else {
                    log_message('error', 'Coluna '.$this->soft_delete_column.' não existe na tabela '.$table.'!');
                }
            }
            return !array_search(false, $delete);
        } else {
            return parent::empty_table($table);
        }
    }

    public function replace_into ($table, $data, $where)
    {

        $data = array_merge($data, $where);

        $replace_query = parent::insert_string($table, $data);
        $replace_query = str_replace('INSERT', 'REPLACE', $replace_query);

        return parent::query($replace_query);

    }

    public function replace ($table = '', $set = NULL)
    {

        if (!is_null($set))
            $this->set($set);

        $keys = array_keys($this->ar_set);

        foreach($keys as $num => $key)
            $update_fields[] = $key .'='. $this->ar_set[$key];

        $this->_reset_write();
        $replace_query = parent::insert_string($table, $data);
        $replace_query .=  " ON DUPLICATE KEY UPDATE ".implode(', ', $update_fields);

        return parent::query($replace_query);

    }

    public function insert_ignore ($table, $data)
    {

        $insert_query = parent::insert_string($table, $data);
        $insert_query = str_replace('INSERT INTO', 'INSERT IGNORE INTO', $insert_query);

        return parent::query($insert_query);

    }

    public function compiled_select()
    {
        return $this->_compile_select();
    }

    public function subquery($alias = FALSE)
    {
        $sql = $this->_compile_select();
        $this->_reset_select();
        return '('.$sql.')'.($alias ? ' AS '.$alias : '');
    }

}
