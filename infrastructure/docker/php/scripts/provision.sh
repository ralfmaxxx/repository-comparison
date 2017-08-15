#!/bin/sh

source services/common.sh
source services/php.sh

update_repositories &&
install_git_and_zip &&
install_php_intl &&
install_php_mysql_pdo &&
install_composer
