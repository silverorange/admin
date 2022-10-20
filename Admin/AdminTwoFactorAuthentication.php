<?php

use RobThree\Auth\TwoFactorAuth;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

/**
 * Admin 2FA
 *
 * @package   Admin
 * @copyright 2022 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminTwoFactorAuthentication
{
	// {{{ public function getNewSecret()

	public function getNewSecret()
	{
		$two_fa = new TwoFactorAuth();
		return $two_fa->createSecret();
	}

	// }}}
	// {{{ public function getQrCodeDataUri()

	public function getQrCodeDataUri($title, $secret, $size = 400)
	{
		$two_fa = new TwoFactorAuth();
		return $two_fa->getQRCodeImageAsDataUri($title, $secret, $size);
	}

	// }}}
	// {{{ public function validateToken()

	public function validateToken($secret, $token, $timeslice)
	{
		// strip all non numeric characters like spaces and dashes that people
		// might enter (e.g. Authy adds spaces for readability)
		$token = preg_replace('/[^0-9]/', '', $token);

		// The timestamp is used to make sure this, or tokens before this,
		// can't be used to authenticate again. There's a "window" of token
		// use and without this, someone could capture the code, and re-use it.
		$two_fa = new TwoFactorAuth();
		$success = $two_fa->verifyCode($secret, $token, 1, null, $timeslice);
		return $success;
	}

	// }}}
}

?>
