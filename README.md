# Welcome to CatalogDemo!

Hi! This is a sample project created using PHP + Symfony and Flutter + GetX applying some concepts of microservices with
functional/unit tests.

Enjoy!

# Environment

To create this project I'm using a microservice's approach using docker containers, so it could be easily switchable 
with any other frontends or backends.

The technologies that were used in this project were:
- Docker: 4.8.1
- PHP: 7.3
- Symfony: 5.4
- Flutter: 3.0.0
- Nginx: stable (alphine)

# How to run

### Docker's container configuration
1. Download and install [docker-desktop](https://docs.docker.com/desktop/#configure-docker-desktop) accordingly to 
your OS
3. Clone this repository in you machine with ```git clone repo.git``` command in your terminal
4. After installing, navigate inside the cloned folder and execute the following commands:
   1. To start/build your docker containers: ```docker-compose up -d```
   2. To stop your docker containers: ```docker-compose down```
5. Edit your **host's** file adding on the last line ```127.0.0.1 catalog.demo.local```
   1. If you're using a Mac or Linux OS, you can locate this file on ```/etc/hosts```
   2. If you're using Windows, you can locate the same file on ```c:\windows\system32\drivers\etc\hosts```
6. Now, you must be already up and running with the entire environment
7. Just connect to [http://catalog.demo.local:8080](http://catalog.demo.local:8080) to access your frontend and 
[http://catalog.demo.local:80](http://catalog.demo.local:80) to your backend

### PHPStorm + Docker containers

The entire PHP environment with composer is available on your docker container, which instead of downloading every lib and 
have the trouble to configure your entire PHP environment from scratch, we can not only use the already built-in environment
but also integrate with our most powerful PHP IDE.

#### Steps to configure remote composer cli:
0. Before we can go any further **make sure to have your docker containers up and running**!
1. Open the project inside your PHPStorm an open your IDE preferences
2. Search for ***PHP*** in the searchbar and select it
3. Now let's click on the `...` button (right side of CLI Interpreters) to configure our ***Remote CLI Interpreter***
4. Click on the plus button to add a new configuration and select the ```From docker, Vagrant...``` option
5. Click on the new button (right side in the Server option)
6. Fill the form with the following options:
   1. Docker Daemon:
      1. Docker for Mac (if you're using Mac)
      2. Unix Socket (if you're using Linux)
      3. Not tested on Windows
7. Now fill out the ***Remote CLI Interpreter*** using the following options:
   1. Server: ```Docker```
   2. Image name: ```catalog-demo_php:latest```
8. After filling the required fields, click on the refresh button to make sure that the php version is ```7.3.15``` and 
click on the apply button
9. With our ***Remote CLI Interpreter*** configured, we can set our composer configuration with the following options:
   1. CLI Interpreter: ```catalog-demo_php:latest```
   2. Path Mappings: ```<Project root>->/app```
10. To finish our configuration, let's click on the folder button (right side of Docker container) and let's edit our 
container path to ```/app``` so it should be like the following:
    1. ```-v <PATH_TO_PROJECT>/api:/app```
11. Select apply, and now you can install/update your application using the Composer cli from the Docker's container

#### Steps to configure remote PHPUnit:
1. Open the project inside your PHPStorm an open your IDE preferences
2. Search for ***PHP*** in the searchbar and select the option ***PHP > Test Frameworks***
3. Click on the plus button to add a new configuration and select the ```From PHPUnit by Remote Interpreter``` option
4. Select our previous configured interpreter and fill out the form with the following option:
   1. CLI Interpreter: ```catalog-demo_php:latest```
   2. Path Mappings: ```<Project root>->/app```
   3. Docker container: ```-v <PATH_TO_PROJECT>/api:/app```
   4. Use Composer autoloader (selected)
   5. Path to script: ```/app/vendor/autoload.php``` (then click on the refresh button)
   6. Default configuration file: ```/app/phpunit.xml.dist```
5. Select apply, and now you can test your application using the PHPUnit from the Docker's container

# Endpoints and Responses

### Response formats

##### # Success

```json
{
   "items": [
      {
         "(String) server-model": [
            {
               "location": "(String) location",
               "products": [
                  {
                     "ramSize": "(String) ramSize", 
                     "ramType": "(String) ramType",
                     "driveQuantity": "(String) driveQuantity",
                     "driveSize": "(String) driveSize",
                     "driveType": "(String) driveType",
                     "price": "(String) price"
                  }
               ]
            }
         ]
      }
   ]
}
```

##### # Error

```json
{
   "code": "(int) code",
   "message": "(String) message",
   "additionalInfo": [
      {
         "line": "(int) line",
         "file": "(String) filePath",
         "trace": "(String) functionTrace",
         "args": [
            {
               "code": "(int) line",
               "message": "(String) message"
            }
         ]
      }
   ]
}
```

### Available endpoints:

#### # GET | HEAD /files/read
- Get available products from already uploaded file or display error requiring the upload of a new spreadsheet
  - To improve performance, a Redis memory cache was implemented to load from memory the file's content
  - If a new file has been uploaded it will check if the cached file is the same, if not it will be updated and sent

#### # POST /files/upload
- Request body:
```json lines
{
   "file": "(binary array) fileContent",
   "fileName": "(String) fileName"
}
```
- Upload a new spreadsheet that will be validated and cached to response
  - All new files are timestamped, so it would create a history of uploaded files 

#### # POST /products/filter
- Request body:
```json lines
{
   "filters": {
      0: { "location":  "(String) location" },
      1: { "driveType":  "(String) driveType" },
      2: { "driveSize":  "(String) driveSize" },
      3: { "ramSize":  "(array) ramSize" },
   }
}
```
- Filter the list of available products with some parameters
  - All parameters are optionals
  - The filter key is used to set a priority while filtering the products data
