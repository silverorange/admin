<?php

require_once("MDB2.php");

/**
 * Database helper class
 *
 * Static convenience methods for working with a database.
 *
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminDB {
	
	/**
	 * Update a field
	 *
 	 * Convenience method to update a single database field for one or more 
	 * rows. One convenient use of this method is for processing SwatActions
	 * that change a single database field.
	 *
	 * @param MDB2_Driver_Common $db The database connection.
	 *
	 * @param string $table The database table to query.
	 *
	 * @param string $field The name of the database field to update. Can be 
	 *        given in the form type:name where type is a standard MDB2 
	 *        datatype. If type is ommitted, then integer is assummed for this 
	 *        field.
	 *
	 * @param mixed $value The value to store in database field $field. The 
	 *        type should correspond to the type of $field.
	 *
	 * @param string $id_field The name of the database field that contains the
	 *        the id. Can be given in the form type:name where type is a
	 *        standard MDB2 datatype. If type is ommitted, then integer is 
	 *        assummed for this field.
	 *
	 * @param Array $ids An array of identifiers corresponding to the database
	 *        rows to be updated. The type of the individual identifiers should 
	 *        correspond to the type of $id_field.
	 */
	public static function update($db, $table, $field, $value, $id_field, $ids) {

		if (count($ids) == 0)
			return;

		$field = new AdminDBField($field, 'integer');
		$id_field = new AdminDBField($id_field, 'integer');

		$sql = 'UPDATE %s SET %s = %s WHERE %s IN (%s)';

		foreach ($ids as &$id)
			$id = $db->quote($id, $id_field->type);

		$id_list = implode(',', $ids);

		$sql = sprintf($sql, 
			$table,
			$field->name,
			$db->quote($value, $field->type),
			$id_field->name,
			$id_list);

		$db->query($sql);
	}

	/**
	 * Query for an option array
	 *
 	 * Convenience method to query for a set of options, each consisting of
	 * an id and a title. The returned option array in the form of
	 * $id => $title can be passed directly to other classes, such as 
	 * SwatFlydown for example.
	 *
	 * @param MDB2_Driver_Common $db The database connection.
	 *
	 * @param string $table The database table to query.
	 *
	 * @param string $title_field The name of the database field to query for 
	 *        the title. Can be given in the form type:name where type is a
	 *        standard MDB2 datatype. If type is ommitted, then string is 
	 *        assummed for this field.
	 *
	 * @param string $id_field The name of the database field to query for 
	 *        the id. Can be given in the form type:name where type is a
	 *        standard MDB2 datatype. If type is ommitted, then integer is 
	 *        assummed for this field.
	 *
	 * @param string $order_by_clause Optional comma deliminated list of 
	 *        database field names to use in the <i>order by</i> clause.
	 *        Do not include "order by" in the string; only include the list
	 *        of field names. Pass null to skip over this paramater.
	 *
	 * @param string $where_clause Optional <i>where</i> clause to limit the 
	 *        returned results.  Do not include "where" in the string; only 
	 *        include the conditionals.
	 *
	 * @return Array An array in the form of $id => $title.
	 */
	public static function getOptionArray($db, $table, $title_field, $id_field, 
		$order_by_clause = null, $where_clause = null) {

		$title_field = new AdminDBField($title_field, 'text');
		$id_field = new AdminDBField($id_field, 'integer');

		$sql = 'select %s, %s from %s';
		$sql = sprintf($sql, $id_field->name, $title_field->name, $table);

		if ($where_clause != null)
			$sql .= ' where '.$where_clause;

		if ($order_by_clause != null)
			$sql .= ' order by '.$order_by_clause;

		$rs = $db->query($sql, array($id_field->type, $title_field->type));

		if (MDB2::isError($rs))
			throw new Exception($rs->getMessage());

		$options = array();

		while ($row = $rs->fetchRow(MDB2_FETCHMODE_OBJECT)) {
			$title_field_name = $title_field->name;
			$id_field_name = $id_field->name;
			$options[$row->$id_field_name] = $row->$title_field_name;
		}

		return $options;
	}

	
}

/**
 * Database field
 * 
 * Data class to represent a database field, a (name, type) pair.
 *
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminDBField {

	/**
	 * @var string The name of the database field.
	 */
	public $name;

	/**
	 * @var string The type of the database field. Any standard MDB2 datatype
	 *      is valid here.
	 */
	public $type;

	/**
	 * @param string $field A string representation of a database field in the
	 *        form [<type>:]<name> where <name> is the name of the database 
	 *        field and <type> is any standard MDB2 datatype.
	 *
	 * @param string $default_type The type to use by default if it is not 
	 *        specified in the $field string. Any standard MDB2 datatype
	 *        is valid here.
	 */
	public function __construct($field, $default_type = 'text') {
		$x = explode(':', $field);

		if (isset($x[1])) {
			$this->name = $x[1];
			$this->type = $x[0];
		} else {
			$this->name = $x[0];
			$this->type = $default_type;
		}
	}

	/**
	 * Get the field as a string
	 *
	 * @return string A string representation of a database field in the
	 *        form <type>:<name> where <name> is the name of the database 
	 *        field and <type> is a standard MDB2 datatype.
	 */
	public function __toString() {
		return $this->type.':'.$this->name;
	}
}

