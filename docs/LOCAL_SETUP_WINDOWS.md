# Setting up a local environment
### A step-by-step guide for Windows computers  

## Download required software
Software | Link
-------- | ----------
Ampps v3.9+ 64-Bit | http://www.ampps.com/downloads
Git For Windows | https://gitforwindows.org/
wget | https://eternallybored.org/misc/wget/

### Common errors
#### MINGW64 bash: wget: command not found
1. Download the wget.exe file from your source
2. Extract to a folder, e.g. C:\Tools\wget\
3. Create a user variable for the exe. In Start Menu, search 'variables'  
   Open the link for "Edit system environment variables". Click 'Environment Variables...'
5. Add new variable (I called it "WGET_HOME" and its value the directory "C:\Tools\wget" )
6. Select the "PATH" variable and append to the list following a ";" "%WGET_HOME%"
7. Click OK. MINGW should be able to find wget

#### MINGW64 bash: php: command not found
1. Download the version of PHP the project is currently using, e.g. php-7.2
2. Extract the folder into the correct Ampps subfolder ~\Program Files\Ampps\php-7.2
3. Now MINGW64 can locate the php.exe file

#### If there is a random file missing like api-ms-win-crt-locale-l1-1-0.dll, try reinstalling Ampps

## Set up an SSH key to your GitHub account

### Adding a new SSH key to your GitHub account
Before adding a new SSH key to your GitHub account, you should:  

* Check for existing SSH keys  
* Generate a new SSH key and add it to the ssh-agent  


#### Check for existing SSH keys
1. Open Git Bash
2. Enter ls -al ~/.ssh to see if existing SSH keys are present:
  ```
  ls -al ~/.ssh
  ```
3. Check the directory listing to see if you already have a public SSH key  
By default, the filenames of the public keys are one of the following:
  * id_rsa.pub
  * id_ecdsa.pub
  * id_ed25519.pub  

If you don't have an existing public and private key pair, or don't wish
to use any that are available to connect to GitHub, then generate a new SSH key
If you have an SSH key to add to your GitHub, you can skip this next step

#### Generating a new SSH key and adding it to the ssh-agent
1. Open Git Bash.
2. Paste the text below, substituting in your GitHub email address.
```
$ ssh-keygen -t rsa -b 4096 -C "your_email@example.com"
```
This creates a new ssh key, using the provided email as a label.  
3. When you're prompted to "Enter a file in which to save the key," press Enter. This accepts the default file location. At the prompt, either type a secure passphrase or leave empty for no passphrase

##### Adding your SSH key to the ssh-agent
Ensure the ssh-agent is running manually by entering:
```
$ eval $(ssh-agent -s)
```
Then, add your SSH private key to the ssh-agent
```
 $ ssh-add ~/.ssh/id_rsa
```
If you created your key with a different name, or if you are adding an existing
key that has a different name, replace id_rsa in the command with the name of your private key file.

##### Adding a new SSH key to your GitHub account
1. Copy the SSH key to your clipboard
If your SSH key file has a different name than the example code, modify the filename to match your
current setup. When copying your key, don't add any newlines or whitespace.  
You can also just copy and paste using your mouse and keyboard
```
$ clip < ~/.ssh/id_rsa.pub
# Copies the contents of the id_rsa.pub file to your clipboard
```
2. In the upper-right corner of any page, click your profile photo, then click Settings
3. In the user settings sidebar, click SSH and GPG keys.
4. Click New SSH key or Add SSH key.
5. In the "Title" field, add a descriptive label for the new key.
6. Paste your key into the "Key" field.
7. Click Add SSH key.

## Clone the repository
Go to the the folder `/c/Program Files/Ampps/www/`

Clone the repository into the `www` folder: `git clone git@github.com:repository_folder.git`  
For example: `git clone git@github.com:OregonStateUniversity/MyEventBoard.git`

## Set .env & run scripts

##### Set up .env file
1. Copy .env.example to ~root/.env  
2. Input passwords where they're missing  
3. Set ENVIRONMENT to development  

ENVIRONMENT should be entirely uppercase  
development should be entirely lowercase

##### Run set up script
In Git Bash (in the My Event Board) folder, run - `bash ./setup.sh`

Now that everything is set up, you can start the running the site on your localhost.  
This simulates hosting the site on the OSU engineering server.

##### Start site locally
1. Connect to the OSU VPN
2. Start Ampps
3. Visit http://localhost in your browser. Click into the project folder and you’re done.
