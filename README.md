ConfigurationBundle
=====================

[![Build Status](https://travis-ci.org/Lp-digital/ConfigurationBundle.svg?branch=master)](https://travis-ci.org/Lp-digital/ConfigurationBundle)
[![Code Climate](https://codeclimate.com/github/Lp-digital/ConfigurationBundle/badges/gpa.svg)](https://codeclimate.com/github/Lp-digital/ConfigurationBundle)
[![Test Coverage](https://codeclimate.com/github/Lp-digital/ConfigurationBundle/badges/coverage.svg)](https://codeclimate.com/github/Lp-digital/ConfigurationBundle/coverage)

**ConfigurationBundle** provides useful configuration per site feature.
This bundle support multi-sites instances.

Installation
------------

Edit the file `composer.json` of your BackBee project.

Add the new dependency to the bundle in the `require` section:
```json
# composer.json
...
    "require": {
        ...
        "lp-digital/configuration-bundle": "dev-master"
    },
...
```

Save and close the file.

Run a composer update on your project.

Then launch the command to update database:
```
./backbee bundle:update conf --force
```

Activation
----------

Edit the file `repository/Config/bundles.yml`of your BackBee project.

Add the following line at the end of the file:
```yaml
# bundles configuration - repository/Config/bundles.yml
...
conf: LpDigital\Bundle\ConfigurationBundle\Configuration
```

Save and close the file.

Depending on your configuration, cache may need to be clear.