<?php

require_once 'Swat/SwatLinkCellRenderer.php';

/**
 * A title link cell renderer for Admin index pages
 *
 * Links in the cell renderer are styled as block-level elements,
 * so other cell renderers in the same table cell may cause layout issues.
 *
 * @package   Admin
 * @copyright 2006-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
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
		$this->addStyleSheet(
			'packages/admin/styles/admin-title-link-cell-renderer.css'
		);
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
	 * - file-save-as
	 * - folder
	 * - folder-with-contents
	 * - person
	 * - product
	 * - download
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

		case 'file-save-as':
			$class = 'admin-title-link-cell-renderer-file-save-as';
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

		case 'download':
			$class = 'admin-title-link-cell-renderer-download';
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

		$this->setStockType();

		parent::render();
	}

	// }}}
	// {{{ protected function setStockType()

	/**
	 * Applies the stock type specificed by the user
	 */
	protected function setStockType()
	{
		if ($this->stock_id !== null) {
			$this->setFromStock($this->stock_id, false);
		}
	}

	// }}}
	// {{{ protected function renderSensitive()

	/**
	 * Renders this link as sensitive
	 */
	protected function renderSensitive()
	{
		$anchor = new SwatHtmlTag('a');
		$anchor->href = $this->getLink();
		$anchor->class = $this->getCSSClassString();
		$anchor->title = $this->getTitle();

		$anchor->open();

		$this->renderContent();

		$anchor->close();
	}

	// }}}
	// {{{ protected function renderInsensitive()

	/**
	 * Renders this link as not sensitive
	 */
	protected function renderInsensitive()
	{
		$span_tag = new SwatHtmlTag('span');
		$span_tag->class = $this->getCSSClassString();
		$span_tag->title = $this->getTitle();

		$span_tag->open();

		$this->renderContent();

		$span_tag->close();
	}

	// }}}
	// {{{ protected function renderContent()

	/**
	 * Renders this link as not sensitive
	 */
	protected function renderContent()
	{
		$icon_span = new SwatHtmlTag('span');
		$icon_span->class = 'admin-title-link-cell-renderer-icon';
		$icon_span->setContent('');
		$icon_span->display();

		$content_span = new SwatHtmlTag('span');
		$content_span->class = 'admin-title-link-cell-renderer-contents';
		$content_span->setContent($this->getText(), $this->content_type);
		$content_span->display();
	}

	// }}}
	// {{{ protected function getCSSClassNames()

	/**
	 * Gets the array of CSS classes that are applied to this user-interface
	 * object
	 *
	 * For AdminTitleLinkCellRenderer objects these classes are applied to the
	 * anchor tag.
	 *
	 * @return array the array of CSS classes that are applied to this
	 *                user-interface object
	 */
	protected function getCSSClassNames()
	{
		$classes = parent::getCSSClassNames();

		$classes[] = 'admin-title-link-cell-renderer';
		if ($this->stock_class !== null)
			$classes[] = $this->stock_class;

		return $classes;
	}

	// }}}
	// {{{ public function getDataSpecificCSSClassNames()

	/**
	 * Gets the data specific CSS class names for this cell renderer
	 *
	 * @return array the array of base CSS class names for this cell renderer.
	 */
	public function getDataSpecificCSSClassNames()
	{
		$classes = array();

		if ($this->stock_class !== null)
			$classes[] = $this->stock_class;

		$classes = array_merge($classes,
			parent::getDataSpecificCSSClassNames());

		return $classes;
	}

	// }}}
	// {{{ public function getBaseCSSClassNames()

	/**
	 * Gets the base CSS class names for this cell renderer
	 *
	 * @return array the array of base CSS class names for this cell renderer.
	 */
	public function getBaseCSSClassNames()
	{
		$classes = parent::getBaseCSSClassNames();
		$classes[] = 'admin-title-link-cell-renderer';
		return $classes;
	}

	// }}}
}

?>
