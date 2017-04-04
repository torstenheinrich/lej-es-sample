# lej-es-sample

This is a sample project using Event sourcing and CQRS with PHP for a
talk I gave back in May 2017 at the [Leipzig Software Development Meetup](https://www.meetup.com/Leipzig-Software-Development-Meetup/).
The slides for this talk can be found [here](LINK).

## Installation

Make sure that you have both [vagrant](https://www.vagrantup.com) and
[VirtualBox](https://www.virtualbox.org) installed.

- Spin up the virtual machine with `vagrant up`.
- Log into the virtual machine with `vagrant ssh`.

Inside the virtual machine, the project can be found at `/home/vagrant/project`.
To make sure all the dependencies of the project are resolved, you have to
install them using [composer](https://getcomposer.org).

- Simply run `composer install` inside the project folder.

## Tests

To demonstrate the concept present in the talk mentioned above, I added a few
test classes. You can run them from inside the project folder.

- Run `vendor/bin/phpunit` to execute a few unit tests.
- Run `php test.php` to execute an integration test with event stores and projections.
