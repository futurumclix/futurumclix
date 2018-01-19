#!/bin/bash

# Copyright (c) 2018 FuturumClix

CAKEVERSION=2.10.6

for i in zip wget unzip
do
	if [ -z "`which $i`" ]
	then
		echo "$i is missing."
		exit 100
	fi
done

FUTURUMVERSION=`cat ./version.txt`

if [ -f "futurumclix-$FUTURUMVERSION.zip" ]
then
	echo "File futurumclix-$FUTURUMVERSION.zip already exists!"
	exit 10
fi

echo -n "Building package futurumclix-$FUTURUMVERSION.zip..."

if [ "`uname`" = "Darwin" ]
then
	TMPDIR="`mktemp -d -t package_futurumclix`"
else
	TMPDIR="`mktemp -d`"
fi

if [ -z "$TMPDIR" ]
then
	echo "Unable to create temporary directory."
	exit 11
fi

function cleanup() {
	rm -rf "$TMPDIR"
	echo "$STATUS"
}

STATUS="failed!"
trap "cleanup" exit

wget "https://github.com/cakephp/cakephp/archive/$CAKEVERSION.zip" -O "$TMPDIR/cakephp.zip" > /dev/null 2>&1 || exit 12
(cd "$TMPDIR" && unzip cakephp.zip > /dev/null 2>&1) || exit 13
cp -R `pwd` "$TMPDIR/dist" > /dev/null 2>&1 || exit 15
cp -R "$TMPDIR/cakephp-$CAKEVERSION/lib" "$TMPDIR/dist/" || exit 16
cp -R "$TMPDIR/cakephp-$CAKEVERSION/.htaccess" "$TMPDIR/dist/" || exit 17
(rm -rf "$TMPDIR/dist/$0" > /dev/null 2>&1 && touch "$TMPDIR/dist/app/Config/database.ini.php" > /dev/null 2>&1) || exit 18
mkdir -p "$TMPDIR/dist/app/tmp/cache/models" "$TMPDIR/dist/app/tmp/cache/persistent" "$TMPDIR/dist/app/tmp/logs" > /dev/null 2>&1 || exit 19
(chmod -R 777 "$TMPDIR/dist/app/tmp" > /dev/null 2>&1 && touch "$TMPDIR/dist/app/Config/core.ini.php") || exit 20
(cd "$TMPDIR/dist" && zip -ro9T "futurumclix-$FUTURUMVERSION.zip" .htaccess * > /dev/null 2>&1) || exit 21
mv "$TMPDIR/dist/futurumclix-$FUTURUMVERSION.zip" ./ > /dev/null 2>&1 || exit 22

STATUS="done."
