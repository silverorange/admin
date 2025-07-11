<?php

use RobThree\Auth\Providers\Qr\BaconQrCodeProvider;
use RobThree\Auth\TwoFactorAuth;

/**
 * Admin 2FA.
 *
 * @copyright 2022 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminTwoFactorAuthentication
{
    public function getNewSecret()
    {
        $two_fa = new TwoFactorAuth(new BaconQrCodeProvider());

        return $two_fa->createSecret();
    }

    public function getQrCodeDataUri($title, $secret, $size = 400)
    {
        $two_fa = new TwoFactorAuth(new BaconQrCodeProvider());

        return $two_fa->getQRCodeImageAsDataUri($title, $secret, $size);
    }

    public function validateToken($secret, $token, &$timeslice)
    {
        // strip all non numeric characters like spaces and dashes that people
        // might enter (e.g. Authy adds spaces for readability)
        $token = preg_replace('/[^0-9]/', '', $token);

        // The timeslice is used to make sure tokens before this
        // can't be used to authenticate again. There's a "window" of token
        // use and without this, someone could capture the code, and re-use it.
        $two_fa = new TwoFactorAuth(new BaconQrCodeProvider());
        $success = $two_fa->verifyCode($secret, $token, 1, null, $timeslice);

        return $success;
    }
}
