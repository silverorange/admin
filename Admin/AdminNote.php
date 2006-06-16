<?php

require_once 'Swat/SwatContentBlock.php';
require_once 'Swat/SwatHtmlTag.php';

/**
 * A widget to display a formatted note in the widget tree
 *
 * @package   Admin
 * @copyright 2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminNote extends SwatContentBlock
{
	// {{{ public properties

	/**
	 * User visible title of this widget
	 *
	 * @var string
	 */
	public $title = '';

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new note
	 *
	 * @param string $id a non-visible unique id for this widget.
	 *
	 * @see SwatWidget::__construct()
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);
		$this->addStyleSheet('admin/styles/admin-note.css');
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this content
	 *
	 * Merely performs an echo of the content.
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		$div = new SwatHtmlTag('div');
		$div->class = 'admin-note';
		$div->open();

		$header_tag = new SwatHtmlTag('h3');
		$header_tag->class = 'admin-note-title';
		$header_tag->setContent($this->title);
		$header_tag->display();

		if ($this->content !== null) {
			$content_div = new SwatHtmlTag('div');
			$content_div->class = 'admin-note-content';
			$content_div->setContent($this->content, $this->content_type);
			$content_div->display();
		}

		$div->close();
	}

	// }}}
}

?>
