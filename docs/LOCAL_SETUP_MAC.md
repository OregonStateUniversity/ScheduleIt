# Setting up a local development environment on Mac

A step-by-step guide for Mac computers.

## Download required software

| Software           | Link                               |
| ------------------ | ---------------------------------- |
| Ampps v3.9+ 64-Bit | http://www.ampps.com/downloads     |
| Git                | https://git-scm.com/download/mac   |
| Wget               | https://www.gnu.org/software/wget/ |

## Set up an SSH key to your GitHub account

### Adding a new SSH key to your GitHub account

Before adding a new SSH key to your GitHub account, you should:

- Check for existing SSH keys
- Generate a new SSH key and add it to the ssh-agent

#### Check for existing SSH keys

1. Open Terminal
2. Enter ls -al ~/.ssh to see if existing SSH keys are present:

```bash
ls -al ~/.ssh
```

3. Check the directory listing to see if you already have a public SSH key  
   By default, the filenames of the public keys are one of the following:

- id_rsa.pub
- id_ecdsa.pub
- id_ed25519.pub

If you don't have an existing public and private key pair, or don't wish
to use any that are available to connect to GitHub, then generate a new SSH key
If you have an SSH key to add to your GitHub, you can skip this next step

#### Generating a new SSH key and adding it to the ssh-agent

1. Open Terminal.
2. Paste the text below, substituting in your GitHub email address.

```bash
$ ssh-keygen -t rsa -b 4096 -C "your_email@example.com"
```

This creates a new ssh key, using the provided email as a label.

3. When you're prompted to "Enter a file in which to save the key," press Enter. This accepts the
   default file location. At the prompt, either type a secure passphrase or leave empty for no passphrase

##### Adding your SSH key to the ssh-agent

Ensure the ssh-agent is running manually by entering:

```bash
$ eval $(ssh-agent -s)
```

Then, add your SSH private key to the ssh-agent

```bash
$ ssh-add ~/.ssh/id_rsa
```

If you created your key with a different name, or if you are adding an existing
key that has a different name, replace id_rsa in the command with the name of your private key file.

##### Adding a new SSH key to your GitHub account

1. Copy the SSH key to your clipboard
   If your SSH key file has a different name than the example code, modify the filename to match your
   current setup. When copying your key, don't add any newlines or whitespace.
   You can also just copy and paste using your mouse and keyboard

```bash
$ pbcopy < ~/.ssh/id_rsa.pub
# Copies the contents of the id_rsa.pub file to your clipboard
```

2. In the upper-right corner of any page, click your profile photo, then click Settings
3. In the user settings sidebar, click SSH and GPG keys.
4. Click New SSH key or Add SSH key.
5. In the "Title" field, add a descriptive label for the new key.
6. Paste your key into the "Key" field.
7. Click Add SSH key.

## Clone the repository

Go to the the folder `/Applications/Ampps/www/`

Clone the repository into the `www` folder: `git clone git@github.com:OregonStateUniversity/MyEventBoard.git`

## Set .env & run scripts

##### Set up .env file

1. Copy `.env.example` to `.env` in the root directory of this application
2. Input passwords where they're missing
3. Set ENVIRONMENT to development

ENVIRONMENT should be entirely uppercase
development should be entirely lowercase

##### Run set up script

In Terminal, in the MyEventBoard folder, run - `sh setup.sh`

Now that everything is set up, you can start the running the site on your localhost.
This simulates hosting the site on the OSU engineering server.

##### Start site locally

1. Connect to the OSU VPN
2. Start Ampps
3. Visit http://localhost in your browser. Click into the project folder and you’re done.

## Local Development vs OSU Server

Setting up a local environment is convenient for development, but the environment isn't exactly the same as an OSU server. Changes to the application should always be tested on an OSU server.
