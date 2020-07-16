# Step by step guide
# How to set up a local environment for
# My Event Board

__Download required software__
Ampps - http://www.ampps.com/downloads

Git For Windows - https://gitforwindows.org/

wget - https://eternallybored.org/misc/wget/
* Note: How to fix - MINGW64 bash: wget: command not found
Follow instructions from user 'daniel martinez' at the following link
https://superuser.com/questions/1075437/mingw64-bash-wget-command-not-found
* Note: How to fix PHP missing error
Download the version of PHP the project is currently using, e.g. php-7.2
Extract the folder into the correct Ampps subfolder, ~\Program Files\Ampps\php-7.2
Now MINGW64 can locate the php.exe file
* Note: If there is a random file missing like api-ms-win-crt-locale-l1-1-0.dll, try reinstalling Ampps

__Set up an SSH key to your GitHub account__
Right-click inside the `www` folder and select `Git Bash Here`

Now that everything is downloaded, follow these instructions
https://docs.github.com/en/github/authenticating-to-github/adding-a-new-ssh-key-to-your-github-account

Clone the repository into the `www` folder: `git clone git@github.com:repository_folder.git`
For example: `git clone git@github.com:OregonStateUniversity/MyEventBoard.git`

__Set up .env file__
Copy ~/config/.env.example to ~/config/.env
Input passwords where they're missing
Set ENVIRONMENT to development
*development should be entirely lowercase*

__Run set up script__
In Git Bash (in the My Event Board) folder, run - `bash ./setup.sh`

Now that everything is set up and configured, you can start the running the site on your localhost.
This simulates the hosting on OSU engineering server.

__Final steps__
* Connect to the OSU VPN
* Start `Ampps`
* Visit http://localhost in your browser. Click into the project folder and you’re done.
