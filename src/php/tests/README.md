(CAUTION: I'm about to go on a rant)

There are no good options for PHP unit tests. This indicates that PHP is truly a dead language. Also maybe that fact is *why* it's just totally not an option for web development. 

Not a lot of people will choose PHP these days, but what about the legacy projects that people want to support but don't want to rewrite in another language? In order to properly revive a project, we need to be able to write tests for the code. There are probably a lot old PHP scripts that have value. And we shouldn't have to ditch all that software just because there is simple, lightweight PHP unit testing option. 

Of the three PHP unit test options that are recommended here:
https://stackoverflow.com/questions/282150/how-do-i-write-unit-tests-in-php

1) PHPUnit is overwrought for small projects (7.5MB, 739 items), the install instructions aren't flexible and didn't work for me on my Mac. Also, most importantly, I'm not going to learn PHPUnit and noone should have to.

2) Simpletest is still way too much, and home page URL doesn't work for me, today, July 19, 2018.

3) phpt requires PEAR, and do people use PEAR anymore? Also, it's meant for testing PHP itself.

The best option, I think, is phpt, but not in it's current state. If someone were to take the script of phpt (run-tests.php) and simplify it to take a folder of tests and output results, that would be the best solution. I actually like format of the tests:

I also couldn't find a good option in this list:
https://en.wikipedia.org/wiki/List_of_unit_testing_frameworks#PHP

Does someone have a recommendation or decent solution? If so, please let me know!







