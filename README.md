# Light-it backend test

## Requirements

* docker
* docker-composer


## Run app 

build the image 
`docker-compose build api`

run tests
`docker-compose run --name lightit_backend_test-migration --rm api /bin/bash -c 'composer exec "pint --test" && php artisan test'`

migrate database tables
`docker-compose run --name lightit_backend_test-migration --rm api /bin/bash -c 'php artisan migrate'`

start api and jobs
`docker-compose up api jobs`


## Run/debug app inside vscode

install extension for vscode-docker: 
`ext install ms-vscode-remote.remote-containers`

open `./api` in vscode


# Email Reader

`http://localhost:8025/`


# Postman Collection

`/postman`

