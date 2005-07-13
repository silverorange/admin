<?php

require_once 'Swat/SwatEntry.php';
require_once 'Admin/Admin.php';

/**
 * A unique text entry widget
 *
 * @package Admin
 * @copyright silverorange 2005
 */
class AdminUniqueEntry extends SwatEntry {

	public $alphanum = true;

	public function init() {
		parent::init();
		$this->size = 20;
	}

	public function process() {
		parent::process();

		if ($this->alphanum && ereg("[^[:alnum:]_]",$this->value)) {

			$msg = Admin::_('The %s field can only contain letters and numbers. Spaces and other special characters are not allowed.');

			$this->addMessage(new SwatMessage($msg, SwatMessage::USER_ERROR));
		}
	}
}

?>
