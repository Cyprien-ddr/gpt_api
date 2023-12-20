#!/bin/bash
### OPTIONS ###
deploy_api=1
deploy_front=1
while getopts af opt; do
  case "$opt" in
  a) deploy_api=1; deploy_front=0 ;; 
  f) deploy_front=1; deploy_api=0 ;; 
  \?) exit 1 ;; esac
done
shift $(($OPTIND - 1))
### OPTIONS ###

if (($deploy_api)); then

  rsync -rvp --exclude=".git" --exclude=".venv" --exclude="config/api_users.yml" api/ gpt@13.37.178.168:/home/gpt/production/
  ssh gpt@13.37.178.168  <<EOF
cd production; 
if [ ! -d .venv ]; then
  python3 -m venv .venv
fi
source .venv/bin/activate
pip install -r requirements.txt
EOF

ssh -t admin@13.37.178.168 "sudo service  gunicorn-gpt restart"

fi

if (($deploy_front)); then

  rsync -rvp --rsync-path="sudo rsync" --exclude=".git" --exclude="client_credentials.json" --exclude="env.php" --exclude="api_token.txt" front/ admin@13.37.178.168:/var/www/gpt-front/production/
  ssh -t admin@13.37.178.168 "sudo chown -R www-data: /var/www/gpt-front/production/"

fi
