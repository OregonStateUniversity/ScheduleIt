# Step by step Windows guide
# How to set up a local environment for
# Schedule It

__Download required software__
Ampps - version 3.9 64-Bit http://www.ampps.com/downloads

Git For Windows - https://gitforwindows.org/

wget - https://eternallybored.org/misc/wget/
* Note: How to fix error - MINGW64 bash: wget: command not found
Download the wget.exe file from your source
Extract to a folder, e.g. C:\Tools\wget\
Create a user variable for the exe. In Start Menu, search 'variables'
Open the link for "Edit system environment variables". Click 'Environment Variables...'
Add new variable (I called it "WGET_HOME" and its value the directory "C:\Tools\wget" )
Select the "PATH" variable and append to the list following a ";" "%WGET_HOME%"
Click OK
Now, MINGW should be able to find wget

* Note: How to fix PHP missing error
Download the version of PHP the project is currently using, e.g. php-7.2
Extract the folder into the correct Ampps subfolder, ~\Program Files\Ampps\php-7.2
Now MINGW64 can locate the php.exe file

* Note: If there is a random file missing like api-ms-win-crt-locale-l1-1-0.dll, try reinstalling Ampps

__Set up an SSH key to your GitHub account__

Right-click inside the `www` folder and select `Git Bash Here`

*Adding a new SSH key to your Github account*
Before adding a new SSH key to your GitHub account, you should have:
* Checked for existing SSH keys
* Generated a new SSH key and added it to the ssh-agent

*Check for existing SSH keys*
Open Git Bash
Enter ls -al ~/.ssh to see if existing SSH keys are present:
Check the directory listing to see if you already have a public SSH key
By default, the filenames of the public keys are one of the following:
* id_rsa.pub
* id_ecdsa.pub
* id_ed25519.pub

If you don't have an existing public and private key pair, or don't wish
to use any that are available to connect to GitHub, then generate a new SSH key
If you have an SSH key to add to your GitHub, you can skip this next step

*Generating a new SSH key and adding it to the ssh-agent*
Open Git Bash.
Paste the text below, substituting in your GitHub email address.
$ ssh-keygen -t rsa -b 4096 -C "your_email@example.com"
This creates a new ssh key, using the provided email as a label.
When you're prompted to "Enter a file in which to save the key," press Enter. This accepts the default file location.
At the prompt, type a secure passphrase, or leave empty for no passphrase

Adding your SSH key to the ssh-agent
Ensure the ssh-agent is running manuallyby entering $ eval $(ssh-agent -s)
Add your SSH private key to the ssh-agent. $ ssh-add ~/.ssh/id_rsa
If you created your key with a different name, or if you are adding an existing
key that has a different name, replace id_rsa in the command with the name of your private key file.

*Adding a new SSH key to your GitHub account*
Copy the SSH key to your clipboard
If your SSH key file has a different name than the example code, modify the filename to match your
current setup. When copying your key, don't add any newlines or whitespace.
$ clip < ~/.ssh/id_rsa.pub
# Copies the contents of the id_rsa.pub file to your clipboard
In the upper-right corner of any page, click your profile photo, then click Settings
In the user settings sidebar, click SSH and GPG keys.
Click New SSH key or Add SSH key.
In the "Title" field, add a descriptive label for the new key. For example, if you're using a personal Mac, you might call this key "Personal MacBook Air".
Paste your key into the "Key" field.
Click Add SSH key.

Clone the repository into the `www` folder: `git clone git@github.com:repository_folder.git`
For example: `git clone git@github.com:OregonStateUniversity/MyEventBoard.git`

__Set up .env file__
Copy .env.example to ~root/.env
Input passwords where they're missing
Set ENVIRONMENT to development
*development should be entirely lowercase*

__Run set up script__
In Git Bash (in the My Event Board) folder, run - `bash ./setup.sh`

Now that everything is set up and configured, you can start the running the site on your localhost.
This simulates the hosting on OSU engineering server.

__Final steps__
* Connect to the OSU VPN
* Start Ampps
* Visit http://localhost in your browser. Click into the project folder and you’re done.
