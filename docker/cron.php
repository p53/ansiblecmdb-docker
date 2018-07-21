# /etc/crontab: system-wide crontab
# Unlike any other crontab you don't have to run the `crontab'
# command to install the new version when you edit this file
# and files in /etc/cron.d. These files also have username fields,
# that none of the other crontabs do.

SHELL=/bin/sh
PATH=/usr/local/sbin:/usr/local/bin:/sbin:/bin:/usr/sbin:/usr/bin

# m h dom mon dow user  command
0 20 * * * app_user . /opt/app_ansiblecmdb_backend/current/.app_env && /opt/app_ansiblecmdb_backend/current/app/console app:redistoelastic:index indash 2>/proc/1/fd/1 1>/proc/1/fd/1
0 21 * * * . /opt/app_ansiblecmdb_backend/venv/bin/activate && ansible all -m setup -i /opt/app_ansiblecmdb_backend/inventory
