Behat Testing
====

Yawik use behat to do browser testing. Here's how to run behat tests:

## Configuration
Copy default behat configuration file:
```bash
$ cp behat.yml.dist behat.yml
```

Edit `base_url` in `behat.yml` configuration files to match your
local yawik installation url: 
```yaml
# change base url to match your location
default:
    ...
    extensions:
        ...
        Behat\MinkExtension:
            # change this base url value to match
            # your local development server url:
            base_url: "http://localhost:8000"
            files_path: "%paths.base%/module/Behat/resources/fixtures/"
```

## Run Behat tests 

Start selenium standalone server with chrome driver:
```bash
$ cd path/to/yawik
$ ./bin/start-selenium.sh
``` 

You can run all scenario with this command:
```bash
$ cd path/to/yawik
$ ./vendor/bin/behat
```

To run behat only for specific feature:
```bash
$ ./vendor/bin/behat features/install.feature
```

To run behat only for specific scenario:
```bash
$ ./vendor/bin/behat features/install.feature:12
```
That command above will run only scenario in `features/install.feature`
line 12
