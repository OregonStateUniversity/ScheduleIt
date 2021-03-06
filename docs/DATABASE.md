# Database

## Setting up a New Database

Set your database credentials in the `.env` file in the root directory of this application. Make a copy of `.env.example` to `.env` if `.env` doesn't exist.

Run migrations to create the database tables and procedures:

```bash
vendor/bin/phinx migrate
```

This command is also included in the `setup.sh` script.

## Creating New Migrations

Database changes are version controlled with migrations using Phinx. To create a new migration, run:

```bash
vendor/bin/phinx create CreateExampleTable
```

The migration should be named in way that clearly states what the migration is doing. For example, if you're altering a table by adding a new column, you can run:

```bash
vendor/bin/phinx create AddColumnNameToExampleTable
```

After creating new migrations, update the database by running:

```bash
vendor/bin/phinx migrate
```

For more information on using Phinx, visit the [Phinx Cookbook](https://book.cakephp.org/phinx/).
