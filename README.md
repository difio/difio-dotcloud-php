Registration agent for Difio, preconfigured for dotCloud / PHP
applications.

It compiles a list of installed packages and sends it to http://www.dif.io.

Installing on your dotCloud PHP application
-------------------------------------------

- Create an account at http://www.dif.io

- Create your PHP application and push it to dotCloud

- Add required dependencies in `dotcloud.yml`

        ...
          requirements:
              - HTTP_Request2-2.0.0
              - PEAR
        ...

- Download the registration script into your application

        cd myapp/
        wget https://raw.github.com/difio/difio-dotcloud-php/master/difio-dotcloud.php -O difio-dotcloud.php
        chmod +x difio-dotcloud.php

- Enable the registration script in your postinstall hook. **Note:**
If you are using an "approot" your `postinstall` script should be in the 
directory pointed by the “approot” directive of your `dotcloud.yml`.
For more information about `postinstall` turn to 
http://docs.dotcloud.com/guides/postinstall/.

If a file named `postinstall` doesn't already exist, create it and add the following:

        #!/bin/sh
        /home/dotcloud/code/difio-dotcloud.php

- Make `postinstall` executable

        chmod a+x postinstall

- Commit your changes (if using git):

        git add .
        git commit -m "enable Difio registration"

- Configure your Difio userID. You can get it from https://difio-otb.rhcloud.com/profiles/mine/.

        dotcloud var set <app name> DIFIO_USER_ID=UserID

- Generate a unique identifier for this application and save the value as environmental variable.

        dotcloud var set <app name> DIFIO_UUID=`uuidgen`

- Then push your application to dotCloud

        dotcloud push <app name>

- If everything goes well you should see something like:

        19:55:10 [www.0] Running postinstall script...
        19:55:13 [www.0] response:200
        19:55:13 [www.0] Difio: Success, registered/updated application with uuid 3bc9eedd-e219-45d0-a9a7-fa9ff658b7f8

- That's it, you can now check your application statistics at http://www.dif.io

Updating the registration agent
-------------------------------

- When a new version of the registration agent script is available simply overwrite your current one

        wget https://raw.github.com/difio/difio-dotcloud-php/master/difio-dotcloud.php -O difio-dotcloud.php
        chmod +x difio-dotcloud.php
        git add . && git commit -m "updated to latest version of difio-dotcloud-php"
        dotcloud push <app name>
