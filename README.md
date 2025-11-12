# SamJUK_AutoLoginAdmin

[![Supported Magento Versions](https://img.shields.io/badge/magento-2.4.4%E2%80%932.4.8-orange.svg?logo=magento)](https://github.com/SamJUK/m2-module-auto-login-admin/actions/workflows/ci.yml)
[![CI Workflow Status](https://github.com/samjuk/m2-module-auto-login-admin/actions/workflows/ci.yml/badge.svg)](https://github.com/SamJUK/m2-module-auto-login-admin/actions/workflows/ci.yml)
[![GitHub Release](https://img.shields.io/github/v/release/SamJUK/m2-module-auto-login-admin?label=Latest%20Release&logo=github)](https://github.com/SamJUK/m2-module-auto-login-admin/releases)

This module removes the Authentication step to access the admin panel, instead directly logs into a pre defined user.

It is aimed to be run within development and/or ephemeral environments only. 

Whilst the module has protections builtin to avoid running in production, this code should never make it there to begin with.

## Installation

Installation may vary depending on your specific environment.

### Composer Dev Dependency
The recommended approach to installing the module is via a composer dev dependency. This should prevent the module from being installed on production/pre-production environments.

```sh
composer require samjuk/m2-module-auto-login-admin
php bin/magento module:enable SamJUK_AutoLoginAdmin
```

### Manual
If for some reason your upstream environments are installing dev dependencies, you can opt for manual installation. Either at build time within your CI, or on the fly during deployment, by running the following from the project root.

```sh
[[ -d .git ]] && echo "app/code/SamJUK/AutoLoginAdmin" >> .git/info/exclude
mkdir -p app/code/SamJUK
git clone https://github.com/SamJUK/m2-module-auto-login-admin app/code/SamJUK/AutoLoginAdmin/
php bin/magento module:enable SamJUK_AutoLoginAdmin
```


## Configuration

The default configuration of the module is to do nothing, even in development environments. With the potential risk involved, I deem, its better for the end user to be implicit in their intent to use this module.

That said, post installation, the module can be enabled with the following command.
```sh
php bin/magento config:set -e samjuk_auto_login_admin/general/auto_login 1
```

A full breakdown of the available configuration is listed within the table below.

Config Path | Default | Description
--- | --- | ---
`samjuk_auto_login_admin/general/auto_login` | `0` | Boolean flag to enable the module
`samjuk_auto_login_admin/general/username` | `admin` | Admin username to Auto Login as
`samjuk_auto_login_admin/general/skip_production_mode_check` | `0` | Boolean flag, for if the module should run in production mode.
