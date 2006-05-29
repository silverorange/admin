<?php

require_once 'Swat/SwatEntry.php';
require_once 'Admin/Admin.php';

/**
 * A unique text entry widget
 *
 * @package   Admin
 * @copyright 2004-2006 silverorange
 */
class AdminUniqueEntry extends SwatEntry
{
	/**
	 * Whether of not this entry is alphanumeric
	 *
	 * This property affects the processing of this control.
	 *
	 * @var boolean
	 *
	 * @see AdminUniqueEntry::process()
	 */
	public $alphanum = true;

	public function init()
	{
		parent::init();
		$this->size = 20;
	}

	/**
	 * Processes this unique entry
	 *
	 * Ensures the value entered by the user is unique and if it is not unique
	 * attaches an error message to the control.
	 */
	public function process()
	{
		parent::process();

		if ($this->alphanum && preg_match('/[^[:alnum:]_]/u', $this->value)) {
			$msg = Admin::_('The %s field can only contain letters and '.
				'numbers. Spaces and other special characters are not '.
				'allowed.');

			$this->addMessage(new SwatMessage($msg, SwatMessage::ERROR));
		}
	}
}

?>
