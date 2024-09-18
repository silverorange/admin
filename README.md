# Admin

Admin is a framework for back-end administration systems. Admin is built using
[Swat](https://github.com/silverorange/swat) and
[Site](https://github.com/silverorange/site).

## Installation

Make sure the silverorange composer repository is added to the `composer.json`
for the project and then run:

```sh
composer require silverorange/admin
```

## Enabling 2FA (Two Factor Authentication)

1. Install the Admin package ≥ `6.1.0`
2. Add two composer packages:

```sh
composer require robthree/twofactorauth
composer require bacon/bacon-qr-code
```

3. Run `composer install`

4. Add the new database fields:

```sql
alter table adminuser add two_fa_secret varchar(255);
alter table adminuser add two_fa_enabled boolean not null default false;
alter table adminuser add two_fa_timeslice integer not null default 0;
```

5. Edit your `.ini` files (both stage and production) and add:

```
[admin]
two_fa_enabled = On
```

6. Let your users know! They will now see 2FA setup in the “Login Settings” in the top-right corner.
