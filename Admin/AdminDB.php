<?php
/**
 * @package Admin
 * @copyright silverorange 2004
 */
require_once("MDB2.php");

/**
 * Static convenience functions for working with a database.
 */
class AdminDB {
	
	public static function update($db, $table, $field, $value, $type, $id_field, $ids, $id_type = 'integer') {

			if (count($ids) == 0)
				return;

			$sql = 'UPDATE %s SET %s = %s WHERE %s IN (%s)';

			foreach ($ids as &$id)
				$id = $db->quote($id, $id_type);

			$id_list = implode(',', $ids);

			$sql = sprintf($sql, 
				$table,
				$field,
				$db->quote($value, $type),
				$id_field,
				$id_list);

			$db->query($sql);
	}
}

