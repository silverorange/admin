<?php
/**
 * @package Admin
 * @copyright silverorange 2004
 */
require_once('Swat/SwatTableViewRow.php');

/**
 * A an extra row containing a "check all" tool.
 */
class AdminTableViewRowCheckAll extends SwatTableViewRow {
	
	public function display(&$columns) {
		echo '<tr>';

		foreach ($columns as $column) {
			$count = 0;

			if ($column->name == 'checkbox') {
				$td_tag = new SwatHtmlTag('td');
				$td_tag->colspan = count($columns) - $count;

				$input_tag = new SwatHtmlTag('input');
				$input_tag->type = 'checkbox';
				$input_tag->name = 'check_all';

				$label_tag = new SwatHtmlTag('label');
				$label_tag->for = 'check_all';

				$td_tag->open();
				$label_tag->open();
				$input_tag->display();
				echo _S('Check All');
				$label_tag->close();
				$td_tag->close();

				break;

			} else {
				$count++;
				echo '<td>&nbsp;</td>';
			}
		}

		echo '</tr>';
	}

}
?>
