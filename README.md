Registration agent for monupco.com, preconfigured for dotCloud / PHP
applications.

It compiles a list of installed packages and sends it to monupco.com.

Installing on your dotCloud PHP application
-------------------------------------------

- Create an account at http://monupco.com

- Create your PHP application in dotCloud

- Add required dependencies in `dotcloud.yml`

        ...
          requirements:
              - HTTP_Request2-2.0.0
        ...

- Download the registration script into your application

        cd myapp/
        wget https://raw.github.com/monupco/monupco-dotcloud-php/master/monupco-dotcloud.php -O monupco-dotcloud.php
        chmod +x monupco-dotcloud.php

- Enable the registration script in your postinstall hook. **Note:**
If you are using an "approot" your `postinstall` script should be in the 
directory pointed by the “approot” directive of your `dotcloud.yml`.
For more information about `postinstall` turn to 
http://docs.dotcloud.com/guides/postinstall/.

If a file named `postinstall` doesn't already exist, create it and add the following:

        #!/bin/sh
        /home/dotcloud/code/monupco-dotcloud.php

- Make `postinstall` executable

        chmod a+x postinstall

- Commit your changes (if using git):

        git add .
        git commit -m "enable monupco registration"

- Set your monupco userID. You can get it from https://monupco-otb.rhcloud.com/profiles/mine/.

        dotcloud var set <app name> MONUPCO_USER_ID=UserID

- Then push your application to dotCloud

        dotcloud push <app name>

- If everything goes well you should see something like:

        19:55:10 [www.0] Running postinstall script...
        19:55:13 [www.0] response:200
        19:55:13 [www.0] Monupco: Success, registered/updated application with id 45

- That's it, you can now check your application statistics at http://monupco.com

Updating the registration agent
-------------------------------

- When a new version of the registration agent script is available simply overwrite your current one

        wget https://raw.github.com/monupco/monupco-dotcloud-php/master/monupco-dotcloud.php -O monupco-dotcloud.php
        chmod +x monupco-dotcloud.php
        git add . && git commit -m "updated to latest version of monupco-dotcloud-php"
        dotcloud push <app name>
