Behat Testing
====

Yawik use behat to do browser testing. Here's how to run behat tests:

#### 1. Configure Behat
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
            files_path: "%paths.base%/module/Behat/resources/fixtures/"
            # change base url to your yawik test server
            base_url: "http://localhost:8000"
```

#### 2. Start selenium standalone server with chrome driver
```bash
$ cd path/to/yawik
$ ./bin/start-selenium.sh
``` 

#### 3. Run behat tests
```bash
$ cd path/to/yawik
$ ./vendor/bin/behat
```
