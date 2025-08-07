#!/bin/bash
# Apply access permissions

if [ ! -f './constants.php' ] || [ ! -d './cli/' ]; then
	echo >&2 '⛔ It does not look like a FreshRSS directory; exiting!'
	exit 2
fi

if [ "$(id -u)" -ne 0 ]; then
	echo >&2 '⛔ Applying access permissions require running as root or sudo!'
	exit 3
fi

# Always fix permissions on the data and extensions directories
# Optionally also fix permissions on the entire webapp
data_path="${DATA_PATH:-./data}"
to_update=("${data_path}")
if [ "${1:-}" = "--only-userdirs" ]; then
	to_update+=("./extensions")
else
	to_update+=(".")
fi

# Based on group access
chown -R :www-data "${to_update[@]}"

# Read files, and directory traversal
chmod -R g+rX "${to_update[@]}"

# Write access to data
mkdir -p "${data_path}/users/_/"
chmod -R g+w "${data_path}"
