<?php

require_once 'Admin/AdminTitleLinkCellRenderer.php';
require_once 'Swat/SwatDateCellRenderer.php';

/**
 * Hybrid swat date/admin title link cell renderer for dates
 *
 * @package   Admin
 * @copyright 2006-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminDateLinkCellRenderer extends AdminTitleLinkCellRenderer
{
	// {{{ public properties

	/**
	 * Date to render
	 *
	 * This may either be a SwatDate object, or may be an ISO-formatted date
	 * string that can be passed into the SwatDate constructor.
	 *
	 * @var string|SwatDate
	 */
	public $date = null;

	/**
	 * Format
	 *
	 * Either a {@link SwatDate} format mask, or class constant. Class
	 * constants are preferable for sites that require translation.
	 *
	 * @var mixed
	 */
	public $format = SwatDate::DF_DATE_TIME;

	/**
	 * Time Zone Format
	 *
	 * A time zone format class constant from SwatDate.
	 *
	 * @var integer
	 */
	public $time_zone_format = null;

	/**
	 * The time zone to render the date in
	 *
	 * The time zone may be specified either as a time zone identifier valid
	 * for DateTimeZone or as a DateTimeZone object. If the render
	 * time zone is null, no time zone conversion is performed.
	 *
	 * @var string|DateTimeZone
	 */
	public $display_time_zone = null;

	// }}}
	// {{{ protected function getText()

	protected function getText()
	{
		$date_renderer = new SwatDateCellRenderer();
		$date_renderer->date = $this->date;
		$date_renderer->format = $this->format;
		$date_renderer->time_zone_format = $this->time_zone_format;
		$date_renderer->display_time_zone = $this->display_time_zone;

		ob_start();
		$date_renderer->render();
		return ob_get_clean();
	}

	// }}}
	// {{{ protected function getTitle()

	protected function getTitle()
	{
		return parent::getText();
	}

	// }}}
}

?>
