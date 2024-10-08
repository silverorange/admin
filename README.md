# Admin

Admin is a framework for back-end administration systems. Admin is built using [Swat](https://github.com/silverorange/swat) and [Site](https://github.com/silverorange/site).

## Installation

Make sure the silverorange composer repository is added to the `composer.json` for the project and then run:

```shell
composer require silverorange/admin
```

## Enabling 2FA (Two-Factor Authentication)

1. Add the new database fields:

   ```sql
   alter table adminuser add two_fa_secret varchar(255);
   alter table adminuser add two_fa_enabled boolean not null default false;
   alter table adminuser add two_fa_timeslice integer not null default 0;
   ```

   These columns are already defined in `sql/tables/AdminUser.sql`, so this is only if you are upgrading from an earlier version of this package and need to add 2FA.

2. Edit your `.ini` files (both stage and production) and add:

   ```ini
   [admin]
   two_fa_enabled = On
   ```

3. Let your users know! They will now see 2FA setup in the “Login Settings” in the top-right corner.
