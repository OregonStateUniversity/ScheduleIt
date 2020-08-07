# Unit Tests

## PHPUnit

We use PHPUnit for unit tests. Test files should be saved in the `tests` folder. Test file names should be in Pascal case suffixed with `Test`. The class name of the test should match the file name.

To run all unit tests, run this command in the root directory of this application:

```bash
bash run_tests_php.sh
```

If you want to automate running unit tests as part of the Git commit process, you can add a `pre-push` hook to your environment:

```bash
cp bin/pre-push .git/hooks/pre-push
chmod 700 .git/hooks/pre-push
```
