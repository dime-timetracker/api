============
Installation
============

The installation of Dime Timetracker server is easy. Just follow the steps.

#. Clone repository into webroot.

   .. code-block:: shell
   
     www> git clone https://github.com/dime-timetracker/server.git
     
#. Change into server directory

   .. code-block:: shell
   
     www> cd server
     
#. The webserver should be able to write to log directory.

   .. code-block:: shell
   
     www/server> chmod a+w data/log
     
#. Install the vendors with composer.

   .. code-block:: shell
   
     www/server> composer install
          
#. Copy parameters.yml.dist to parameters.yml.

   .. code-block:: shell
   
     www/server> cp app/parameters.yml.dist app/parameters.yml
   
#. Edit parameters.yml and add you database credentials.

   .. code-block:: shell
   
     www/server> vim app/parameters.yml
     --
     # Database
     database:
       driver:    "mysql"
       host:      "127.0.0.1"
       database:  "Dime"
       username:  "Your database user"
       password:  "Your password" 
       charset:   "utf8"
       collation: "utf8_general_ci"
       prefix:
       
#. Create mysql database.

#. Open you browser and go to the server location and open the route "http://domain.to/server/migrate".