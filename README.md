# ClearVUE - Test
[![Continuous Integration](https://github.com/xedinunknown/clearvue-test/actions/workflows/continuous-integration.yml/badge.svg)](https://github.com/xedinunknown/clearvue-test/actions/workflows/continuous-integration.yml)

A test project for [ClearVUE][].

## Getting Started
1. Pull the repo.
2. Copy `.env.example` to `.env`, noting and possibly tweaking configuration. This includes access credentials.
3. Run `docker compose build` to build the containers.
4. Run `docker compose run --rm build make build` to build the project.
5. Run `docker-compose up` to bring up the project, by default on `localhost`. 
   Access the various services through their respective ports.

## Notes
Started with [`dhii/php-project`][].


[ClearVUE]: https://clearvue.business/
[`dhii/php-project`]: https://github.com/Dhii/php-project
