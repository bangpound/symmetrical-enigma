Something something
===================

* `docker-compose up consul`
* Visit http://localhost:8500 and add some keys under `httpd` and `php`:
	* `httpd/DOCUMENT_ROOT` = `/usr/local/apache2/htdocs`
	* `php/APP_ENV` = `dev`
* `docker-compose up`
* Visit `http://localhost:8000/env/dump` to see the values you defined for `php`.
