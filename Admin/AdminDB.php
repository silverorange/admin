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
	
	public static function update($db, $table, $field, $value, $type, 
		$id_field, $ids, $id_type = 'integer') {

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

	public static function getOptionArray($db, $table, $title_field, $title_type, 
		$id_field, $id_type = 'integer', $where_clause = '') {

		$sql = 'SELECT %s, %s FROM %s';
		$sql = sprintf($sql, $id_field, $title_field, $table);

		if (strlen($where_clause))
			$sql .= ' WHERE '.$where_clause;

		$rs = $db->query($sql, array($id_type, $title_type));

		if (MDB2::isError($rs))
			throw new Exception($rs->getMessage());

		$options = array();

		while ($row = $rs->fetchRow(MDB2_FETCHMODE_OBJECT))
			$options[$row->$id_field] = $row->$title_field;

		return $options;
	}

}

