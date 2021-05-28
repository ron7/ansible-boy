# AnsibleBoy - Ansible Frontend Hub

About
-----

AnsibleBoy aims to use the Asnible `facts` as data, which can then be visualized as a table

![](/assets/ansibleBoyScreen1.png?raw=true "Screenshot")

![](/assets/ansibleBoyScreen2.png?raw=true "Screenshot")


ToDo
------
(note that this project is very fresh)

- Ability to export in CSV or HTML
- Extend the data
- Add options to see each host separately
- Add custom facts
- Add docker-compose setup
- Others

How to install
------

- needs PHP installed (7.4 recommended)
- needs ansible installed
- assumes that your ansible setup utilizes ssh-keys to login to the hosts


This is a straight forward PHP setup, so clone the repo directly in a `web` folder.
Setup `.env` file from the example `.example.env` one
Create a Database with a user.
Import the `.db_users_init` into the DB, which will create the `users` table, and add the initial `admin` user. TODO: this should be automated in the code, and not be a manual step.
Login to `/admin` with user: `admin` and pass `admin`. Make sure to change these.
Once in the Users menu, click the red button to `Reset DB Table Servers`, which recreates the table every time. So be careful with it.

In `.env` you would need to setup the absolute paths to your`inventry` file, and `ansible.cfg` files, so the `.cron` can use both of these files to connect to your inventory and pull the `facts`.

You can also use real time environment variables like:
```shell
ANSIBLEBOY_INVENTORY=/path/to/hosts ANSIBLEBOY_CFG=/path/to/ansible.cfg  ./.cron
```
in case you need to do this multiple times.

Then set that cron to to run at the interval you want to gather new `facts` from your fleet.

