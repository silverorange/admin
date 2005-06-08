<?php

require_once 'Swat/SwatEntry.php';

/**
 * A unique text entry widget
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class AdminUniqueEntry extends SwatEntry {

	public $alphanum = true;

	public function init() {
		parent::init();
		$this->size = 20;
	}

	public function process() {
		parent::process();

		if ($this->alphanum && ereg("[^[:alnum:]]",$this->value)) {
			$msg = _S("The %s field can only contain letters and numbers. Spaces and other special characters are not allowed.");
			$this->addMessage(new SwatMessage($msg, SwatMessage::USER_ERROR));
		}
	}
}

?>
