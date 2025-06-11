<?php

/**
 * An entry in an admin dependency
 *
 * @package   Admin
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminDependencyEntry extends AdminDependencyItem
{


	/**
	 * Identifier for this entry
	 *
	 * This is usually a database primary key value or a single field value in
	 * a binding table.
	 *
	 * @var mixed
	 */
	public $id;

	/**
	 * Title for display
	 *
	 * @var string
	 */
	public $title;

	/**
	 * Content type of the title
	 *
	 * Defaults to 'text/plain'. Use 'text/xml' for XHTML fragments.
	 *
	 * @var string
	 */
	public $content_type = 'text/plain';




	/**
	 * Creates a new AdminDependencyEntry
	 *
	 * This constructor enables the entry to be used in a MDB2 data wrapper
	 * to automatically create objects from a result set.
	 *
	 * @param mixed $data the MDB2 row containing the data for this entry
	 *                     object.
	 */
	public function __construct($data = null)
	{
		if ($data !== null) {
			$this->id = $data->id;
			$this->title = $data->title;
			$this->parent = $data->parent;
			$this->status_level = $data->status_level;
		}
	}


}

?>
