
###   Yawik is currently PHP based. We have realized that we can reach our goal faster without PHP. we'll see how it is with nodejs. We concentrate on the input of [job postings](https://cross-solution.de/jobpost), [job search](https://cross-solution.de/jobs), creating a [CV](https://cross-solution.de/cv) and [applying by form](https://cross-solution.de/cv). If you like the idea Open Source for human resources, [support us](https://github.com/sponsors/cbleek)

YAWIK
=====

YAWIK offers a Web based solution for managing job applications. Jobs ads can
be entered or pushed to the system. The system assigns application forms to job
ads. Applicants and recruiters can connect to YAWIK using social networks. 
Currently it is possible to integrate YAWIK into a corporate Web site by 
extending it with a module. It is intended to become a distributed system for 
connecting recruiters and applicants.

Current state
-------------

[alpha](https://de.wikipedia.org/wiki/Entwicklungsstadium_(Software)#Alpha-Version)

but

The following Jobboards are using YAWIK:

* https://www.gastrojob24.ch
* https://www.stellenmarkt.com

you want to be listet here? Send a PR.

Build status: 

[![Build Status](https://api.travis-ci.org/cross-solution/YAWIK.svg)](https://travis-ci.org/cross-solution/YAWIK)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/cross-solution/YAWIK/badges/quality-score.png?b=develop)](https://scrutinizer-ci.com/g/cross-solution/YAWIK/?branch=develop)
[![Coverage Status](https://coveralls.io/repos/cross-solution/YAWIK/badge.svg?branch=develop)](https://coveralls.io/r/cross-solution/YAWIK?branch=develop)
[![Average time to resolve an issue](http://isitmaintained.com/badge/resolution/cross-solution/YAWIK.svg)](http://isitmaintained.com/project/cross-solution/YAWIK "Average time to resolve an issue")
[![Percentage of issues still open](http://isitmaintained.com/badge/open/cross-solution/YAWIK.svg)](http://isitmaintained.com/project/cross-solution/YAWIK "Percentage of issues still open")



Demo
----

http://yawik.org/demo/ (this demonstrates YAWIK as an applicant tracking system)
http://jobs.yawik.org (YAWIK as a Jobportal with real job offers) 


Documentation
-------------

http://yawik.readthedocs.org/en/latest/

API Documentation
-----------------

http://yawik.org/docs/

Forum
-----

https://forum.yawik.org


:muscle: Contribute!
--------------------

* [Fork and clone](https://help.github.com/articles/fork-a-repo).
* Run the command `./install.sh``, which downloads composer and runs `php composer.phar install` to install the dependencies. 
  This will also install the dev dependencies. See [Composer](https://getcomposer.org/doc/03-cli.md#install).
* Use the command `phpunit` in the tests directory to run the tests. See [PHPUnit](http://phpunit.de).
* Create a branch, commit, push and send us a [pull request](https://help.github.com/articles/using-pull-requests).

Mailinglist for Developers
--------------------------

yawik-dev @ googlemail . com
