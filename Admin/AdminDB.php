<?php

require_once("MDB2.php");
require_once("Admin/AdminDBField.php");
require_once("Swat/SwatTreeNode.php");

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
	 *        standard MDB2 datatype. If type is ommitted, then text is 
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

	/**
	 * Query for an option tree array
	 *
 	 * Convenience method to query for a set of options, each consisting of
	 * an id, levelnum, and a title. The returned option array in the form of
	 * a collection of {@link SwatTreeNode}s to other classes, such as 
	 * SwatFlydown for example.
	 *
	 * @param MDB2_Driver_Common $db The database connection.
	 *
	 * @param string $sp Stored procedure/function to execute. Must return the
	 *        values: id, title, level - in the order of output.
	 *
	 * @param string $title_field The name of the database field to query for 
	 *        the title. Can be given in the form type:name where type is a
	 *        standard MDB2 datatype. If type is ommitted, then text is 
	 *        assummed for this field.
	 *
	 * @param string $id_field The name of the database field to query for 
	 *        the id. Can be given in the form type:name where type is a
	 *        standard MDB2 datatype. If type is ommitted, then integer is 
	 *        assummed for this field.
	 *
	 * @param string $parent_field The name of the database field to query for 
	 *        the parent. Can be given in the form type:name where type is a
	 *        standard MDB2 datatype. If type is ommitted, then integer is 
	 *        assummed for this field.
	 *
	 * @return SwatTreeNode A tree hierarchy of {@link SwatTreeNode}s
	 */
	public static function getOptionArrayTree($db, $sp, $title_field, $id_field,
		$level_field) {

		$id_field = new AdminDBField($id_field, 'integer');
		$title_field = new AdminDBField($title_field, 'text');
		$level_field = new AdminDBField($level_field, 'integer');
		
		$types = array($id_field->type, $title_field->type, $level_field->type);
		
		$rs = $db->executeStoredProc($sp, array(0), $types, true);
		if (MDB2::isError($rs))
			throw new Exception($rs->getMessage());

		$tree = AdminDB::buildOptionArrayTree($rs, $title_field->name, $id_field->name, $level_field->name);
		return $tree;
	}

	private static function buildOptionArrayTree($rs, $title_field_name, $id_field_name,
		$level_field_name) {

		$stack = array();
		$current_parent =  new SwatTreeNode(0, 'root');
		$base_parent = $current_parent;
		array_push($stack, $current_parent);
		$last_node = $current_parent;	

		while ($row = $rs->fetchRow(MDB2_FETCHMODE_OBJECT)) {
			$title = $row->$title_field_name;
			$id = $row->$id_field_name;
			$level = $row->$level_field_name;
			
			if ($level > count($stack)) {
				array_push($stack, $current_parent);
				$current_parent = $last_node;
			} else if ($level < count($stack)) {
				$current_parent = array_pop($stack);
			}
		
			$last_node = new SwatTreeNode(array('title'=>$title));
			$current_parent->children[$id] = $last_node;
		}

		return $base_parent;
	}

