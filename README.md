# Read Me

- - - -


Develop By: DanTheCoder

email: contact@danthecoder.com

Website: http://danthecoder.com


- - - -

PHP version 7.2
Require oAuth key files
Use DB dump: sql/20200313_forge_dump.sql

After update DB settings data
- php artisan cache:clear
- php artisan config:clear
- composer remove vendor/package

- - - -

Copyright (c) 2019 HelpCommerce.com

Notes:
- Stripe plan ID will be an (int) that correlates to the id in the plans table.  You should create a plan in the app, it will then create a new product + plan in Stripe.

