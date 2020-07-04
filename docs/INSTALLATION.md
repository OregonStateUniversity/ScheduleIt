# Installation

## Setting Up the Repository for Development

For the project to be functional, it should be cloned into the 'public_html' folder in your engineering filespace.  
MyEventBoard in its current state is supposed to run only on the engineering servers, as requested by the client.

There are a few things to install. Run the Bash shell commands below to install Composer and some packages.  
MyEventBoard uses Twig, Mimey, and phpCAS. Install Composer first, and then install the packages.

```
./setup.sh
```

Make a copy of `config/.env.example` to `config/.env`. Set values for passwords and environment.

Remember to also set permissions for everything inside the repository. Run the script `./set_permissions.sh`.
