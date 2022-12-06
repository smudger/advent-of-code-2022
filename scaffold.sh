#!/bin/bash

RED="\033[01;31m"
GREEN="\033[1;32m"
NO_COLOUR="\033[0m"

[[ -z "$1" ]] && {
  echo -e "${RED}Please specify day number to scaffold.${NO_COLOUR}";
  exit 1;
}

day=$1

[[ -f "./tests/Day${day}Test.php" || -d "./src/Day${day}" ]] && {
  echo -e "${RED}This day has already been scaffolded.${NO_COLOUR}";
  exit 1;
}

echo "Scaffolding day $day..."

cp -r "./template/DayX" "./src/Day${day}"
sed -i "" "s/DayX/Day${day}/g" "./src/Day${day}/Puzzle1.php"
sed -i "" "s/DayX/Day${day}/g" "./src/Day${day}/Puzzle2.php"

cp "./template/DayXTest.php" "./tests/Day${day}Test.php"
sed -i "" "s/DayX/Day${day}/g" "./tests/Day${day}Test.php"

echo -e "${GREEN}Day $day scaffolded. Good luck!${NO_COLOUR}"

