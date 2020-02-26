# OpenLiteSpeed WordPress Docker Container
[![Build Status](https://travis-ci.com/litespeedtech/ols-docker-env.svg?branch=master)](https://hub.docker.com/r/litespeedtech/openlitespeed)
[![OpenLiteSpeed](https://img.shields.io/badge/openlitespeed-1.6.9-informational?style=flat&color=blue)](https://hub.docker.com/r/litespeedtech/openlitespeed)
[![docker pulls](https://img.shields.io/docker/pulls/litespeedtech/openlitespeed?style=flat&color=blue)](https://hub.docker.com/r/litespeedtech/openlitespeed)
[![beta pulls](https://img.shields.io/docker/pulls/litespeedtech/openlitespeed-beta?label=beta%20pulls)](https://hub.docker.com/r/litespeedtech/openlitespeed-beta)

Install a Lightweight WordPress container with OpenLiteSpeed [Edge / Stable] version based on Ubuntu 18.04 Linux.

### Prerequisites
1. [Install Docker](https://www.docker.com/)
2. [Install Docker Compose](https://docs.docker.com/compose/)
3. Clone this repository or copy the files from this repository into a new folder:
```
git clone https://github.com/litespeedtech/ols-docker-env.git
```

## Configuration
Edit the `.env` file to update the demo site domain, default MySQL user, and password.

## Installation
Open a terminal, `cd` to the folder in which `docker-compose.yml` is saved, and run:
```
docker-compose up
```

## Components
The docker image installs the following packages on your system:

|Component|Version|
| :-------------: | :-------------: |
|Linux|Ubuntu 18.04|
|OpenLiteSpeed|[Latest version](https://openlitespeed.org/downloads/)|
|MariaDB|[Stable version: 10.3](https://hub.docker.com/_/mariadb)|
|PHP|[Stable version: 7.4](http://rpms.litespeedtech.com/debian/)|
|LiteSpeed Cache|[Latest from WordPress.org](https://wordpress.org/plugins/litespeed-cache/)|
|ACME|[Latest from ACME official](https://github.com/acmesh-official/get.acme.sh)|
|WordPress|[Latest from WordPress](https://wordpress.org/download/)|
|phpMyAdmin|[Latest from dockerhub](https://hub.docker.com/r/bitnami/phpmyadmin/)|

## Data Structure
Cloned project 
```bash
├── acme
├── bin
│   └── container
├── data
│   └── db
├── logs
│   ├── access.log
│   ├── error.log
│   ├── lsrestart.log
│   └── stderr.log
├── lsws
│   ├── admin-conf
│   └── conf
└── sites
│   └── localhost
├── LICENSE
├── README.md
├── docker-compose.yml
```
  * **acme**  contains all applied the cert from Lets Encrypt
  * **bin**   contains multiple cli scripts to allow you add/del VH, install applications, upgrade ..etc 
  * **data**  Stores mysql db
  * **logs**  contains all the webserver logs and virtual host access logs
  * **lsws**  contains all web server config files
  * **sites** contains the Document root (the WordPress application will install here)

## Usage
### Starting a Container
Start the container with the `up` or `start` methods:
```
docker-compose up
```
You can run with daemon mode, like so:
```
docker-compose up -d
```
The container is now built and running. 

### Stopping a Container
```
docker-compose stop
```
### Removing Containers
To stop and remove all containers, use the `down` command:
```
docker-compose down
```
### Setting the WebAdmin Password
We strongly recommend you set your personal password right away.
```
bash bin/webadmin.sh MYPASSWORD
```
### Starting a Demo Site
After running the following command, you should be able to access the WordPress installation with the configured domain. By default the domain is `https://localhost` and `https://server_IP`.
```
bash bin/demosite.sh
```
### Creating a Domain and Virtual Host
```
bash bin/domain.sh [-add|-a] example.com
```
### Deleting a Domain and Virtual Host
```
bash bin/domain.sh [-del|-d] example.com
```
### Creating a Database
You can either automatically generate the user, password, and database names, or specify them. Use the following to auto generate:
```
bash bin/database.sh [-domain|-d] example.com
```
Use this command to specify your own names, substituting `user_name`, `my_password`, and `database_name` with your preferred values:
```
bash bin/database.sh [-domain|-d] example.com [-user|-u] user_name [-password|-p] my_password [-database|-db] database_name
```
### Installing a WordPress Site
To preconfigure the `wp-config` file, run the `database.sh` script for your domain, before you use the following command to install WordPress:
```
./bin/appinstall.sh [-app|-a] wordpress [-domain|-d] example.com
```

### Install ACME 
We need to run amce installation command at **first time only**. 
*  With email notification
```
./bin/acme.sh [--install|-i] [--email|-e] EMAIL_ADDR
```
*  Without email notification
```
./bin/acme.sh [--install|-i] [--no-email|-ne]
```
### Applying a Let's Encrypt Certificate
Use the root domain in this command, and it will check for a certificate and automatically apply one with and without `www`:
```
./bin/acme.sh [-domain|-d] example.com
```
### Update Web Server
To upgrade web server to latest stable version, run
```
bash bin/webadmin.sh [-lsup|-upgrade]
```

### Apply OWASP ModSecurity
Enable OWASP mod_secure on web server, run 
```
bash bin/webadmin.sh [-modsec|-sec] enable
```
Disable OWASP mod_secure on web server, run 
```
bash bin/webadmin.sh [-modsec|-sec] disable
```
### Accessing the Database
After installation, you can use phpMinAdmin to access the database by visiting http://127.0.0.1:8080 or https://127.0.0.1:8443. The default username is `root`, and the password is the same as the one you supplied in the `.env` file.

## Support & Feedback
If you still have a question after using OpenLiteSpeed Docker, you have a few options.
* Join [the GoLiteSpeed Slack community](litespeedtech.com/slack) for real-time discussion
* Post to [the OpenLiteSpeed Forums](https://forum.openlitespeed.org/) for community support
* Reporting any issue on [Github ols-docker-env](https://github.com/litespeedtech/ols-docker-env/issues) project

**Pull requests are always welcome** 
