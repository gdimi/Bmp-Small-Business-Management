bmp
===
Hello y'all, this is a small software made in php/js/sql/css/html.
It's purpose is to manage a small business's afairs: customers, expenses, income etc.
It organises day to day task in a case tracker and has statistcs.
It is still in beta stage so bugs are expected and welcomed!


Notes and installation
======================
Installation: just clone or download this repository and place the files in a directory in a webserver. Then point to it from your browser

Database: this software uses an sqlite database for maximum portability. There is a folder named pld/ which has the phpliteadmin tool to manage the db. The default password is croatoan.

Authentication: there is no user system so either do not throw it on www, or use htacess auth in front of it!

Security: this codebase is checked for basic security with RIPS community edition and PHPstan

Standards: this code losely conforms to PSR-0 and PSR-1 standards

Tested up to php 7.4

Licence: gpl 2.0


George Dimitrakopoulos 2014 - 2023
