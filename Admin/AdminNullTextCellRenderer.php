<?php

require_once 'Swat/SwatTextCellRenderer.php';

/**
 * A cell renderer that displays a message if it is asked to display
 * null text
 *
 * @package   admin
 * @copyright 2005-2006 silverorange
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
	 * Whether to test the {@link SwatTextCellRenderer::$text} property for
	 * null using strict equality.
	 *
	 * @var boolean
	 */
	public $strict = false;

	/**
	 * Renders this cell renderer
	 */
	public function render()
	{
		if (($this->strict && $this->text === null) ||
			(!$this->strict && $this->text == null)) {

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
