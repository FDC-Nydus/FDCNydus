cd /vagrant/workspace/FDCDevRepo && git fetch origin master
cd /vagrant/workspace/FDCDevRepo && git reset --hard FETCH_HEAD
cd /vagrant/workspace/FDCDevRepo && git clean -df
cd /vagrant/workspace/FDCDevRepo && git reset --hard origin/master 2>&1; echo "status_code="$?