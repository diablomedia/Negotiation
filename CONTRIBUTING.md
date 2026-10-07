Contributing
============

First of all, **thank you** for contributing, **you are awesome**!

Here are a few rules to follow in order to ease code reviews, and discussions before
maintainers accept and merge your work.

Follow [PER Coding Style 3.0](https://www.php-fig.org/per/coding-style/), enforced
by the project's PHP CS Fixer configuration. Run `composer cs:fix` to format changes.

Run `composer check` before submitting a pull request. This runs PHPUnit, PHPStan,
and the coding standard check. Tests and static analysis must pass on PHP 8.2–8.5,
including the lowest compatible dependency versions (`composer update --prefer-lowest --prefer-stable`).

Write or update unit tests when changing library behavior.

You SHOULD write documentation.

Please, write [commit messages that make
sense](http://tbaggery.com/2008/04/19/a-note-about-git-commit-messages.html),
and [rebase your branch](http://git-scm.com/book/en/Git-Branching-Rebasing)
before submitting your Pull Request.

One may ask you to [squash your
commits](http://gitready.com/advanced/2009/02/10/squashing-commits-with-rebase.html)
too. This is used to "clean" your Pull Request before merging it (we don't want
commits such as `fix tests`, `fix 2`, `fix 3`, etc.).

Also, while creating your Pull Request on GitHub, you MUST write a description
which gives the context and/or explains why you are creating it.

Thank you!
