<?php

require_once 'Swat/SwatTextCellRenderer.php';

/**
 * A cell renderer that displays a message if it is asked to display
 * null text
 *
 * @package   admin
 * @copyright 2005 silverorange
 */
class AdminNullTextCellRenderer extends SwatTextCellRenderer
{
	/**
	 * The text to display in this cell if the
	 * {@link SwatTextCellRenderer::$text} proeprty is null when the render()
	 * method is called
	 *
	 * @var string
	 */
	public $null_text = '&lt;none&gt;';

	/**
	 * Renders this cell renderer
	 */
	public function render()
	{
		if ($this->text === null) {
			$this->text = $this->null_text;

			echo '<span class="admin-null-text-cell-renderer-null">';
			parent::render();
			echo '</span>';

		} else {
			parent::render();
		}
	}
}

?>
