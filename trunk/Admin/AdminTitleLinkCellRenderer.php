<?php

require_once 'Swat/SwatLinkCellRenderer.php';

/**
 * A title link cell renderer for Admin index pages
 *
 * Links in the cell renderer are styled as block-level elements, 
 * so other cell renderers in the same table cell may cause layout issues.
 *
 * @package   Admin
 * @copyright 2006 silverorange
 */
class AdminTitleLinkCellRenderer extends SwatLinkCellRenderer
{
	// {{{ public properties

	/**
	 * The stock id of this AdminTitleCellRenderer
	 *
	 * Specifying a stock id initializes this title link renderer with a set of
	 * stock values.
	 *
	 * @var string
	 *
	 * @see AdminTitleCellRenderer::setFromStock()
	 */
	public $stock_id = null;

	// }}}
	// {{{ protected properties

	/**
	 * A CSS class set by the stock_id of this title link cell renderer
	 *
	 * @var string
	 */
	protected $stock_class = null;

	/**
	 * The last stock_id used in a render call
	 *
	 * @var string
	 */
	protected $last_stock_id = null;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a title link cell renderer
	 */
	public function __construct()
	{
		parent::__construct();
		$this->addStyleSheet('packages/admin/styles/admin-title-link-cell-renderer.css',
			Admin::PACKAGE_ID);
	}

	// }}}
	// {{{ public function setFromStock()

	/**
	 * Sets the values of this title link cell renderer to a stock type
	 *
	 * Valid stock type ids are:
	 *
	 * - document (default)
	 * - document-with-contents
	 * - edit
	 * - folder
	 * - folder-with-contents
	 * - person
	 * - product
	 *
	 * @param string $stock_id the identifier of the stock type to use.
	 * @param boolean $overwrite_properties whether to overwrite properties if
	 *                                       they are already set.
	 *
	 * @throws SwatUndefinedStockTypeException
	 */
	public function setFromStock($stock_id, $overwrite_properties = true)
	{
		if ($stock_id === $this->last_stock_id)
			return;

		$class = null;

		switch ($stock_id) {
		case 'document':
			$class = 'admin-title-link-cell-renderer-document';
			break;

		case 'document-with-contents':
			$class = 'admin-title-link-cell-renderer-document-with-contents';
			break;

		case 'edit':
			$class = 'admin-title-link-cell-renderer-edit';
			break;

		case 'folder-with-contents':
			$class = 'admin-title-link-cell-renderer-folder-with-contents';
			break;

		case 'folder':
			$class = 'admin-title-link-cell-renderer-folder';
			break;

		case 'person':
			$class = 'admin-title-link-cell-renderer-person';
			break;

		case 'product':
			$class = 'admin-title-link-cell-renderer-product';
			break;

		default:
			throw new SwatUndefinedStockTypeException(
				"Stock type with id of '{$stock_id}' not found.",
				0, $stock_id);
		}

		$this->stock_class = $class;
		$this->last_stock_id = $stock_id;
	}

	// }}}
	// {{{ public function getThAttributes()

	/**
	 * Gets TH-tag attributes
	 *
	 * Sub-classes can redefine this to set attributes on the TH tag.
	 *
	 * The returned array is of the form 'attribute' => value.
	 *
	 * @return array an array of attributes to apply to the TH tag of the
	 *                column that contains this cell renderer.
	 */
	public function getThAttributes()
	{
		return array('class' => 'admin-title-link-cell-renderer');
	}

	// }}}
	// {{{ public function init()

	/**
	 * Initializes this admin title link cell renderer
	 */
	public function init()
	{
		parent::init();

		if ($this->stock_id === null)
			$this->setFromStock('document', false);
		else
			$this->setFromStock($this->stock_id, false);
	}

	// }}}
	// {{{ public function render()

	/**
	 * Renders the contents of this cell
	 *
	 * @see SwatCellRenderer::render()
	 */
	public function render()
	{
		if (!$this->visible)
			return;

		if ($this->stock_id !== null)
			$this->setFromStock($this->stock_id, false);

		$contents_span = new SwatHtmlTag('span');
		$contents_span->class = 'admin-title-link-cell-renderer-contents';
		$contents_span->setContent($this->getText(), $this->content_type);

		if ($this->sensitive && ($this->link !== null)) {
			$anchor = new SwatHtmlTag('a');
			$anchor->href = $this->getLink();
			if ($this->stock_class !== null)
				$anchor->class = $this->stock_class.' ';

			$anchor->class.= 'admin-title-link-cell-renderer';
			$anchor->title = $this->getTitle();

			$anchor->open();
			$contents_span->display();
			$anchor->close();
		} else {
			$span_tag = new SwatHtmlTag('span');
			$span_tag->class = 'swat-link-cell-renderer-insensitive';
			$span_tag->title = $this->getTitle();
			if ($this->stock_class !== null)
				$span_tag->class.= ' '.$this->stock_class;

			$span_tag->open();
			$contents_span->display();
			$span_tag->close();
		}
	}

	// }}}
	// {{{ public function getBaseCSSClassNames()

	/**
	 * Gets additional CSS classes for this cell renderer's TD tag
	 *
	 * @return array an array of CSS class names to apply to the TD tag of the
	 *                column that contains this cell renderer.
	 */
	public function getBaseCSSClassNames()
	{
		$classes = array('admin-title-link-cell-renderer');

		if ($this->stock_class !== null)
			$classes[] = $this->stock_class;

		return $classes;
	}

	// }}}
}

?>
