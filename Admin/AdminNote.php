<?php

/**
 * A widget to display a formatted note in the widget tree
 *
 * @package   Admin
 * @copyright 2006-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminNote extends SwatContentBlock
{


	/**
	 * User visible title of this widget
	 *
	 * @var string
	 */
	public $title = '';



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
		$this->addStyleSheet('packages/admin/styles/admin-note.css');
	}



	/**
	 * Displays this content
	 *
	 * Merely performs an echo of the content.
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		SwatWidget::display();

		$div = new SwatHtmlTag('div');
		$div->id = $this->id;
		$div->class = $this->getCSSClassString();
		$div->open();

		if ($this->title != '') {
			$header_tag = new SwatHtmlTag('h3');
			$header_tag->class = 'admin-note-title';
			$header_tag->setContent($this->title);
			$header_tag->display();
		}

		if ($this->content != '') {
			$content_div = new SwatHtmlTag('div');
			$content_div->class = 'admin-note-content';
			$content_div->setContent($this->content, $this->content_type);
			$content_div->display();
		}

		$div->close();
	}



	/**
	 * Gets the array of CSS classes that are applied to this note
	 *
	 * @return array the array of CSS classes that are applied to this note.
	 */
	protected function getCSSClassNames()
	{
		$classes = ['admin-note'];
		$classes = array_merge($classes, $this->classes);
		return $classes;
	}

}

?>
