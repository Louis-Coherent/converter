Restarting Supervisor

sudo supervisorctl restart ci4-queue-worker


Malicious file detecton 

sudo apt update && sudo apt install clamav clamav-daemon
sudo systemctl start clamav-freshclam  # Update virus database
sudo systemctl enable clamav-daemon 

sudo apt update
sudo apt install clamav-daemon -y
sudo systemctl start clamav-daemon
sudo systemctl enable clamav-daemon
