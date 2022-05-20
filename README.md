**Nate Flynn**
# WordPress Core Framework
Codename: YORI
___

## What is it?

This is a development framework for WordPress sites. It's designed to give a solid starting point for custom themes and plugins using Object Oriented code and a cleaner, more consistent way of bootstrapping theme and plugin development.

In a nutshell, this framework provides some base classes for themes and plugins that handle the loading and basic setup from a JSON config file, similar to WordPress's `theme.json` setup.
___

## How it works

By itself this plugin doesn't do much. It simply exposes the classes and frameworks for you to use to develop more consistent themes and plugins.

### Using this framework, you can:

- Bootstrap a new Theme or Plugin in seconds by extending a base Theme or Plugin class;
- Extend WordPress Core Post Types programatically;
- Create Custom Post Types & Taxonomies with JSON config files;
- Group Post Types into Categories in WP Admin; and
- Generate Plugin Settings Pages using JSON config files and Gutenberg components.
___

## Installation

To install the Framework, you should download the repo to your local machine using `git clone`, then unpack it into your `mu-plugins` directory in your local WordPress installation.

After this, you'll need to move the file `nateflynn-wp-core.php` out of the plugin directory and into your `mu-plugins` folder. This handles loading the MU Plugin files directly, as WordPress currently doesn't support loading multi-file MU Plugins.

> **Note**
> 
> It's important that this plugin is installed in the `mu-plugins` directory so it's codebase is available before your regular plugins or theme are loaded by WordPress. 
> 
> Installing this plugin to the `plugins` directory may lead to features or functionality not being available for other parts of your site that may rely on it.
___

## Disclaimer

This project is a work in progress. You're welcome to fork the base project, submit PRs or submit Feature Requests through the Conversations tab in this repo.

> This framework is provided as-is and is not intended to provide any additional functionality to WordPress on it's own.
___

## Contributing

You're welcome to contribute to this project by submitting a Pull Request (PR). Before submitting a PR, please ensure all code has been thoroughly tested up to and including the latest stable release of Wordpress. We currently support up to WordPress 5.9 and intend to extend that support to WordPress 6.0 on or shortly after it's public release.

We use semantic versioning to denote the current stable version of the project. Any unstable or in-development branches should be marked as `alpha` or `beta` depending on their stability.

Development should be done within a versioned branch (ie. `1.x` or `2.x`).

> **You don't need to be a developer to contribute!**
> 
> There are bound to be spellign mistakes, mislabeled comments or inconsistent variable names throughout the project. If you spot one feel free to amend it and submit a PR. You'll still be listed as a contributor!
___

## License

This project, like WordPress, is distributed under GPL licensing. Feel free to download, clone, modify use or redistribute it as you see fit. The only request is that you leave file headers and attributions where they are so ourselves and our contributors can be attributed for their work!