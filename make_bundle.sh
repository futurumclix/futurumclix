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
rm -rf "$TMPDIR/dist/$0" > /dev/null 2>&1 || exit 17
(cd "$TMPDIR/dist" && zip -ro9T "futurumclix-$FUTURUMVERSION.zip" * > /dev/null 2>&1) || exit 18
mv "$TMPDIR/dist/futurumclix-$FUTURUMVERSION.zip" ./ > /dev/null 2>&1 || exit 19

STATUS="done."
